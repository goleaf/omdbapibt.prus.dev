<?php

namespace App\Console\Commands;

use App\Services\OmdbApiKeyManager;
use Illuminate\Console\Command;

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

    public function __construct(protected OmdbApiKeyManager $manager)
    {
        parent::__construct();
    }

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
        $this->components->task('Checking pending keys', function (): void {
            $minimum = (int) config('services.omdb.bruteforce.min_pending_keys', 10000);
            $charset = (string) config('services.omdb.bruteforce.charset', '0123456789abcdefghijklmnopqrstuvwxyz');
            $keyLength = (int) config('services.omdb.bruteforce.key_length', 8);
            $batchSize = (int) config('services.omdb.bruteforce.generation_batch', 1000);

            $pendingBefore = $this->manager->countPendingKeys();

            if ($pendingBefore >= $minimum) {
                $this->info(sprintf('Sufficient keys available (%d pending)', $pendingBefore));

                return;
            }

            $generated = $this->manager->generatePendingKeys($minimum, $charset, $keyLength, $batchSize);
            $pendingAfter = $this->manager->countPendingKeys();

            if ($generated === 0) {
                $this->components->warn('No new keys were generated.');

                return;
            }

            $this->components->info(sprintf(
                'Generated %d key(s); pending pool increased from %d to %d.',
                $generated,
                $pendingBefore,
                $pendingAfter
            ));
        });

        $this->newLine();
    }

    /**
     * Validate pending keys using async HTTP requests with resume capability.
     */
    protected function validatePendingKeys(): void
    {
        $checkpoint = $this->manager->currentValidationCheckpoint();
        $this->components->info(sprintf('Resuming validation from checkpoint: %d', $checkpoint));

        $batchSize = (int) config('services.omdb.validation.batch_size', 50);
        $testImdbId = (string) config('services.omdb.validation.test_imdb_id', 'tt3896198');
        $timeout = (int) config('services.omdb.validation.timeout', 10);
        $baseUrl = rtrim((string) config('services.omdb.base_url', 'http://www.omdbapi.com'), '/');

        $totalProcessed = 0;
        $totalValid = 0;
        $totalInvalid = 0;
        $totalUnknown = 0;

        while (true) {
            $result = $this->manager->validatePendingKeys($batchSize, $testImdbId, $timeout, $baseUrl, $checkpoint);

            if ($result['processed'] === 0) {
                if ($totalProcessed === 0) {
                    $this->components->info('All keys validated!');
                }

                break;
            }

            $range = $result['range'];
            $this->line(sprintf(
                'Validating batch of %d keys%s...',
                $result['processed'],
                $range['start'] !== null && $range['end'] !== null
                    ? sprintf(' (IDs %d to %d)', $range['start'], $range['end'])
                    : ''
            ));

            $totalProcessed += $result['processed'];
            $totalValid += $result['valid'];
            $totalInvalid += $result['invalid'];
            $totalUnknown += $result['unknown'];

            $checkpoint = $result['checkpoint'];

            $this->components->info(sprintf(
                'Batch complete. Checkpoint: %d | Valid: %d | Invalid: %d | Unknown: %d | Total: %d',
                $checkpoint,
                $totalValid,
                $totalInvalid,
                $totalUnknown,
                $totalProcessed
            ));

            sleep(1);
        }

        $this->newLine();
        $this->components->info(sprintf(
            'Validation complete! Processed: %d | Valid: %d | Invalid: %d | Unknown: %d',
            $totalProcessed,
            $totalValid,
            $totalInvalid,
            $totalUnknown
        ));
        $this->newLine();
    }

    /**
     * Parse movies using valid API keys.
     */
    protected function parseMovies(): void
    {
        $limit = (int) config('services.omdb.bruteforce.movie_limit', 1000);
        $chunk = (int) config('services.omdb.bruteforce.movie_chunk', 50);
        $timeout = (int) config('services.omdb.validation.timeout', 10);
        $baseUrl = rtrim((string) config('services.omdb.base_url', 'http://www.omdbapi.com'), '/');

        $result = $this->manager->parseMoviesWithKeys($limit, $chunk, $baseUrl, $timeout);

        if ($result['status'] === 'no_keys') {
            $this->components->warn('No valid API keys available for parsing.');

            return;
        }

        if ($result['status'] === 'no_movies') {
            $this->components->info('No movies need updating.');

            return;
        }

        $this->components->info(sprintf(
            'Parsing movies with %d valid key(s) across %d candidate(s).',
            $result['key_count'],
            $result['candidates']
        ));

        $this->components->info(sprintf(
            'Movie parsing complete! Processed: %d | Updated: %d',
            $result['processed'],
            $result['updated']
        ));
        $this->newLine();
    }
}
