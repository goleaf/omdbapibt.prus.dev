<?php

namespace App\Services;

use App\Models\Movie;
use App\Models\OmdbApiKey;
use Illuminate\Cache\Repository as CacheRepository;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Throwable;

class OmdbApiKeyManager
{
    public const VALIDATION_CHECKPOINT_CACHE_KEY = 'services:omdb:validation:checkpoint';

    public function __construct(
        protected HttpFactory $http,
        protected CacheRepository $cache,
        protected ConnectionInterface $connection
    ) {
    }

    /**
     * Ensure the pending key pool meets the minimum threshold.
     *
     * @return int The number of newly generated records.
     */
    public function generatePendingKeys(
        int $minimumPending,
        string $charset,
        int $length,
        int $batchSize
    ): int {
        $pendingCount = OmdbApiKey::pending()->count();

        if ($pendingCount >= $minimumPending) {
            return 0;
        }

        $needed = $minimumPending - $pendingCount;
        $generated = 0;

        while ($generated < $needed) {
            $batch = [];
            $remaining = $needed - $generated;

            for ($i = 0; $i < min($batchSize, $remaining); $i++) {
                $batch[] = [
                    'key' => $this->generateKey($charset, $length),
                    'status' => OmdbApiKey::STATUS_PENDING,
                    'first_seen_at' => Carbon::now(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }

            $generated += $this->connection->table('omdb_api_keys')->insertOrIgnore($batch);
        }

        return $generated;
    }

    /**
     * Validate batches of pending keys by probing the OMDb API.
     *
     * @return array{processed: int, valid: int, invalid: int, checkpoint: int}
     */
    public function validatePendingKeys(
        int $batchSize,
        string $imdbId,
        int $timeout,
        string $baseUrl,
        ?int $startFrom = null
    ): array {
        $checkpoint = $startFrom ?? (int) $this->cache->get(self::VALIDATION_CHECKPOINT_CACHE_KEY, 0);

        $keys = OmdbApiKey::pending()
            ->when($checkpoint > 0, fn ($query) => $query->where('id', '>', $checkpoint))
            ->orderBy('id')
            ->limit($batchSize)
            ->get();

        if ($keys->isEmpty()) {
            return [
                'processed' => 0,
                'valid' => 0,
                'invalid' => 0,
                'checkpoint' => $checkpoint,
            ];
        }

        $responses = $this->http->pool(function (Pool $pool) use ($keys, $imdbId, $timeout, $baseUrl): void {
            foreach ($keys as $key) {
                $pool->as((string) $key->getKey())
                    ->withOptions([
                        'timeout' => $timeout,
                        'connect_timeout' => min(5, $timeout),
                    ])
                    ->get($baseUrl, [
                        'i' => $imdbId,
                        'apikey' => $key->key,
                    ]);
            }
        });

        $stats = [
            'processed' => 0,
            'valid' => 0,
            'invalid' => 0,
            'checkpoint' => $checkpoint,
        ];

        foreach ($keys as $candidate) {
            $response = $responses[(string) $candidate->getKey()] ?? null;

            $result = $this->processValidationResponse($candidate, $response);

            $stats['processed']++;

            if ($result === OmdbApiKey::STATUS_VALID) {
                $stats['valid']++;
            }

            if ($result === OmdbApiKey::STATUS_INVALID) {
                $stats['invalid']++;
            }

            $stats['checkpoint'] = (int) $candidate->getKey();
        }

        $this->cache->forever(self::VALIDATION_CHECKPOINT_CACHE_KEY, $stats['checkpoint']);

        return $stats;
    }

    /**
     * Import keys from a remote endpoint.
     *
     * @return array{imported: int, skipped: int}
     */
    public function importFromRemote(string $url, ?string $jsonField = null): array
    {
        $response = $this->http->get($url)->throw();

        $payload = trim($response->body());
        $candidates = [];

        if ($payload === '') {
            return ['imported' => 0, 'skipped' => 0];
        }

        if ($this->looksLikeJson($payload)) {
            $decoded = json_decode($payload, true, flags: JSON_THROW_ON_ERROR);

            $keys = $jsonField !== null ? Arr::get($decoded, $jsonField, []) : $decoded;
            $candidates = array_filter(array_map('strval', is_array($keys) ? $keys : []));
        } else {
            $candidates = array_filter(array_map('trim', preg_split('/\r?\n/', $payload) ?: []));
        }

        if (empty($candidates)) {
            return ['imported' => 0, 'skipped' => 0];
        }

        $existing = OmdbApiKey::query()
            ->whereIn('key', $candidates)
            ->pluck('key')
            ->all();

        $newKeys = array_values(array_diff($candidates, $existing));
        $records = [];

        foreach ($newKeys as $key) {
            $records[] = [
                'key' => $key,
                'status' => OmdbApiKey::STATUS_PENDING,
                'first_seen_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        if (! empty($records)) {
            $this->connection->table('omdb_api_keys')->insertOrIgnore($records);
        }

        return ['imported' => count($records), 'skipped' => count($candidates) - count($records)];
    }

    /**
     * Parse movies using available valid keys.
     *
     * @return array{processed: int, updated: int}
     */
    public function parseMoviesWithKeys(
        int $movieLimit,
        int $chunkSize,
        string $baseUrl,
        int $timeout
    ): array {
        $validKeys = OmdbApiKey::valid()
            ->orderByDesc('last_confirmed_at')
            ->pluck('key')
            ->all();

        if (empty($validKeys)) {
            return ['processed' => 0, 'updated' => 0];
        }

        $movies = Movie::query()
            ->where(function ($query): void {
                $query->whereNull('plot')
                    ->orWhere('updated_at', '<', Carbon::now()->subDays(30));
            })
            ->limit($movieLimit)
            ->get();

        if ($movies->isEmpty()) {
            return ['processed' => 0, 'updated' => 0];
        }

        $processed = 0;
        $updated = 0;
        $keyCount = count($validKeys);
        $keyIndex = 0;

        foreach ($movies->chunk($chunkSize) as $chunk) {
            $responses = $this->http->pool(function (Pool $pool) use (&$keyIndex, $keyCount, $validKeys, $chunk, $timeout, $baseUrl): void {
                foreach ($chunk as $movie) {
                    $apiKey = $validKeys[$keyIndex % $keyCount];
                    $keyIndex++;

                    $pool->as((string) $movie->getKey())
                        ->withOptions([
                            'timeout' => $timeout,
                            'connect_timeout' => min(5, $timeout),
                        ])
                        ->get($baseUrl, [
                            'i' => $movie->imdb_id,
                            'apikey' => $apiKey,
                        ]);
                }
            });

            foreach ($chunk as $movie) {
                $response = $responses[(string) $movie->getKey()] ?? null;

                if ($this->updateMovieFromResponse($movie, $response)) {
                    $updated++;
                }

                $processed++;
            }
        }

        return ['processed' => $processed, 'updated' => $updated];
    }

    protected function generateKey(string $charset, int $length): string
    {
        $characters = str_split($charset);
        $max = count($characters) - 1;
        $key = '';

        for ($i = 0; $i < $length; $i++) {
            $key .= $characters[random_int(0, $max)];
        }

        return $key;
    }

    protected function processValidationResponse(OmdbApiKey $key, mixed $response): string
    {
        if (! $response instanceof Response) {
            return $this->markUnknown($key, $response instanceof Throwable ? $response->getCode() : null);
        }

        if ($response->failed()) {
            return $this->markUnknown($key, $response->status());
        }

        $payload = $response->json();

        if (is_array($payload) && ($payload['Response'] ?? null) === 'True') {
            $key->forceFill([
                'status' => OmdbApiKey::STATUS_VALID,
                'last_checked_at' => Carbon::now(),
                'last_confirmed_at' => Carbon::now(),
                'last_response_code' => $response->status(),
            ])->save();

            return OmdbApiKey::STATUS_VALID;
        }

        if ($this->responseIndicatesInvalid($response, $payload)) {
            $key->forceFill([
                'status' => OmdbApiKey::STATUS_INVALID,
                'last_checked_at' => Carbon::now(),
                'last_response_code' => $response->status(),
            ])->save();

            return OmdbApiKey::STATUS_INVALID;
        }

        return $this->markUnknown($key, $response->status());
    }

    protected function markUnknown(OmdbApiKey $key, ?int $status): string
    {
        $key->forceFill([
            'status' => OmdbApiKey::STATUS_UNKNOWN,
            'last_checked_at' => Carbon::now(),
            'last_response_code' => $status,
        ])->save();

        return OmdbApiKey::STATUS_UNKNOWN;
    }

    protected function responseIndicatesInvalid(Response $response, mixed $payload): bool
    {
        if ($response->status() === 401) {
            return true;
        }

        $body = is_array($payload) ? (string) ($payload['Error'] ?? '') : (string) $response->body();

        return Str::contains(strtolower($body), 'invalid api key');
    }

    protected function updateMovieFromResponse(Movie $movie, mixed $response): bool
    {
        if (! $response instanceof Response || $response->failed()) {
            return false;
        }

        $payload = $response->json();

        if (! is_array($payload) || ($payload['Response'] ?? null) !== 'True') {
            return false;
        }

        $movie->forceFill([
            'title' => $payload['Title'] ?? $movie->title,
            'year' => $payload['Year'] ?? $movie->year,
            'plot' => $payload['Plot'] ?? $movie->plot,
            'poster_path' => $payload['Poster'] ?? $movie->poster_path,
            'vote_average' => $payload['imdbRating'] ?? $movie->vote_average,
            'updated_at' => Carbon::now(),
        ])->save();

        return true;
    }

    protected function looksLikeJson(string $payload): bool
    {
        return str_starts_with($payload, '{') || str_starts_with($payload, '[');
    }
}
