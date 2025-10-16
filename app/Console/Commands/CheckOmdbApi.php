<?php

namespace App\Console\Commands;

use App\Models\OmdbApiKey;
use Illuminate\Console\Command;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Throwable;

class CheckOmdbApi extends Command
{
    /**
     * The maximum number of keys to check per batch.
     */
    private const BATCH_SIZE = 10;

    /**
     * Cache key for persisting the last processed candidate identifier.
     */
    private const PROGRESS_CACHE_KEY = 'commands:check-omdb-api:checkpoint';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'checkapi {--from= : Candidate primary key to resume processing from}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Probe batches of candidate OMDb API keys and persist confirmed working keys.';

    public function __construct(
        protected HttpFactory $http,
        protected OmdbApiKey $keys,
        protected CacheRepository $progress
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $requestedStart = $this->option('from');
        $checkpoint = $requestedStart !== null
            ? (int) $requestedStart
            : (int) $this->progress->get(self::PROGRESS_CACHE_KEY, 0);

        $this->components->info('Starting OMDb API key verification.');
        $this->line(sprintf('Current checkpoint: %d', $checkpoint));

        $totalProcessed = 0;
        $totalSuccess = 0;
        $totalInvalid = 0;

        while (true) {
            $batch = $this->nextBatch($checkpoint);

            if ($batch->isEmpty()) {
                if ($totalProcessed === 0) {
                    $this->warn('No candidate keys were found to evaluate.');
                }

                break;
            }

            $startKey = $batch->first()->getKey();
            $endKey = $batch->last()->getKey();

            $this->line(sprintf(
                'Processing %d key(s) spanning primary keys %d through %d.',
                $batch->count(),
                $startKey,
                $endKey
            ));

            $responses = $this->http->pool(function (Pool $pool) use ($batch) {
                foreach ($batch as $candidate) {
                    $pool->as((string) $candidate->getKey())
                        ->withOptions([
                            'connect_timeout' => 5,
                            'timeout' => 10,
                        ])
                        ->get('https://www.omdbapi.com/', [
                            'i' => 'tt12788488',
                            'apikey' => $candidate->key,
                        ]);
                }
            });

            [$batchSuccess, $batchInvalid] = $this->processResponses($batch, $responses);

            $totalProcessed += $batch->count();
            $totalSuccess += $batchSuccess;
            $totalInvalid += $batchInvalid;
            $checkpoint = (int) $batch->last()->getKey();

            $this->progress->forever(self::PROGRESS_CACHE_KEY, $checkpoint);

            $this->components->info(sprintf(
                'Batch complete — %d success, %d invalid. Progress saved at checkpoint %d.',
                $batchSuccess,
                $batchInvalid,
                $checkpoint
            ));

            $this->line(sprintf('Resume hint: php artisan checkapi --from=%d', $checkpoint));
        }

        $this->components->info(sprintf(
            'Verification complete. Processed %d candidate(s) with %d confirmed and %d rejected.',
            $totalProcessed,
            $totalSuccess,
            $totalInvalid
        ));

        if ($totalProcessed > 0) {
            $this->line(sprintf(
                'Next run will continue from checkpoint %d unless overridden with --from.',
                $checkpoint
            ));
        }

        return self::SUCCESS;
    }

    /**
     * Retrieve the next batch of candidate keys using the stored checkpoint.
     */
    protected function nextBatch(int $checkpoint): Collection
    {
        $primaryKey = $this->keys->getKeyName();

        return $this->keys->newQuery()
            ->pending()
            ->when($checkpoint > 0, function ($query) use ($primaryKey, $checkpoint) {
                $query->where($primaryKey, '>', $checkpoint);
            })
            ->orderBy($primaryKey)
            ->limit(self::BATCH_SIZE)
            ->get();
    }

    /**
     * Process the responses returned from the pool execution.
     *
     * @param  array<string, Response>  $responses
     * @return array{int, int}
     */
    protected function processResponses(Collection $batch, array $responses): array
    {
        $success = 0;
        $invalid = 0;

        foreach ($batch as $candidate) {
            $response = $responses[(string) $candidate->getKey()] ?? null;

            if (! $response instanceof Response) {
                $this->error(sprintf('Missing response for candidate #%d.', $candidate->getKey()));
                continue;
            }

            try {
                if ($this->responseIndicatesSuccess($response)) {
                    $candidate->forceFill([
                        'status' => OmdbApiKey::STATUS_VALID,
                        'last_checked_at' => now(),
                        'last_confirmed_at' => now(),
                    ])->save();

                    $this->info(sprintf('✓ Key %s is valid.', $this->maskKey($candidate->key)));
                    ++$success;

                    continue;
                }

                if ($this->responseIndicatesInvalidKey($response)) {
                    $candidate->forceFill([
                        'status' => OmdbApiKey::STATUS_INVALID,
                        'last_checked_at' => now(),
                    ])->save();

                    $this->warn(sprintf('✗ Key %s is invalid.', $this->maskKey($candidate->key)));
                    ++$invalid;

                    continue;
                }

                $candidate->forceFill([
                    'status' => OmdbApiKey::STATUS_UNKNOWN,
                    'last_checked_at' => now(),
                ])->save();

                $this->warn(sprintf(
                    'Received unexpected response for key %s (status %d).',
                    $this->maskKey($candidate->key),
                    $response->status()
                ));
            } catch (Throwable $exception) {
                $this->error(sprintf(
                    'Error processing key %s: %s',
                    $this->maskKey($candidate->key),
                    $exception->getMessage()
                ));
            }
        }

        return [$success, $invalid];
    }

    /**
     * Determine if the response indicates a valid API key.
     */
    protected function responseIndicatesSuccess(Response $response): bool
    {
        if ($response->failed()) {
            return false;
        }

        $payload = $response->json();

        return is_array($payload) && ($payload['Response'] ?? null) === 'True';
    }

    /**
     * Determine if the response indicates the key is invalid.
     */
    protected function responseIndicatesInvalidKey(Response $response): bool
    {
        if ($response->status() === 401) {
            return true;
        }

        $payload = $response->json();

        if (! is_array($payload)) {
            return Str::contains(strtolower($response->body()), 'invalid api key');
        }

        $errorMessage = (string) ($payload['Error'] ?? '');

        return Str::contains(strtolower($errorMessage), 'invalid api key');
    }

    /**
     * Mask the majority of the API key before printing to the console.
     */
    protected function maskKey(string $key): string
    {
        if (strlen($key) <= 4) {
            return str_repeat('*', max(strlen($key) - 1, 0)).substr($key, -1);
        }

        return substr($key, 0, 2).str_repeat('*', max(strlen($key) - 4, 0)).substr($key, -2);
    }
}
