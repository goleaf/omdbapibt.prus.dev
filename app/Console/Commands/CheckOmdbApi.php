<?php

namespace App\Console\Commands;

use App\Services\OmdbApiKeyManager;
use Illuminate\Console\Command;

class CheckOmdbApi extends Command
{
    /**
     * Cache key for persisting the last processed candidate identifier.
     */
    private const PROGRESS_CACHE_KEY = OmdbApiKeyManager::VALIDATION_CHECKPOINT_CACHE_KEY;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'checkapi
        {--from= : Candidate primary key to resume processing from}
        {--batch= : Maximum number of keys to validate per batch}
        {--timeout= : Timeout in seconds for each OMDb validation request}
        {--imdb= : IMDb identifier used when probing candidate keys}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Probe batches of candidate OMDb API keys and persist confirmed working keys.';

    public function __construct(protected OmdbApiKeyManager $manager)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $requestedStart = $this->option('from');
        $checkpoint = $requestedStart !== null
            ? max(0, (int) $requestedStart)
            : $this->manager->currentValidationCheckpoint();

        $this->components->info('Starting OMDb API key verification.');
        $this->line(sprintf('Current checkpoint: %d', $checkpoint));

        $totalProcessed = 0;
        $totalSuccess = 0;
        $totalInvalid = 0;
        $totalUnknown = 0;

        $batchSize = $this->determineBatchSize();
        $timeout = $this->determineTimeout();
        $testImdbId = $this->determineTestImdbId();
        $baseUrl = $this->determineBaseUrl();

        while (true) {
            $result = $this->manager->validatePendingKeys($batchSize, $testImdbId, $timeout, $baseUrl, $checkpoint);

            if ($result['processed'] === 0) {
                if ($totalProcessed === 0) {
                    $this->warn('No candidate keys were found to evaluate.');
                }

                break;
            }

            $range = $result['range'];

            $this->line(sprintf(
                'Processing %d key(s)%s.',
                $result['processed'],
                $range['start'] !== null && $range['end'] !== null
                    ? sprintf(' spanning primary keys %d through %d', $range['start'], $range['end'])
                    : ''
            ));

            $totalProcessed += $result['processed'];
            $totalSuccess += $result['valid'];
            $totalInvalid += $result['invalid'];
            $totalUnknown += $result['unknown'];
            $checkpoint = $result['checkpoint'];

            $this->components->info(sprintf(
                'Batch complete — %d success, %d invalid, %d unknown. Progress saved at checkpoint %d.',
                $result['valid'],
                $result['invalid'],
                $result['unknown'],
                $checkpoint
            ));

            $this->line(sprintf('Resume hint: php artisan checkapi --from=%d', $checkpoint));

            sleep(1);
        }

        $this->components->info(sprintf(
            'Verification complete. Processed %d candidate(s) with %d confirmed, %d rejected, and %d marked unknown.',
            $totalProcessed,
            $totalSuccess,
            $totalInvalid,
            $totalUnknown
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
     * Resolve the batch size for each iteration.
     */
    protected function determineBatchSize(): int
    {
        $override = $this->option('batch');

        if ($override !== null) {
            return max(1, (int) $override);
        }

        return max(1, (int) config('services.omdb.validation.batch_size', 10));
    }

    /**
     * Resolve the timeout applied to OMDb validation requests.
     */
    protected function determineTimeout(): int
    {
        $override = $this->option('timeout');

        if ($override !== null) {
            return max(1, (int) $override);
        }

        return max(1, (int) config('services.omdb.validation.timeout', 10));
    }

    /**
     * Resolve the IMDb identifier used when probing candidate keys.
     */
    protected function determineTestImdbId(): string
    {
        return (string) ($this->option('imdb') ?? config('services.omdb.validation.test_imdb_id', 'tt3896198'));
    }

    /**
     * Resolve the OMDb base URL from configuration.
     */
    protected function determineBaseUrl(): string
    {
        return rtrim((string) config('services.omdb.base_url', 'https://www.omdbapi.com/'), '/');
    }
}
