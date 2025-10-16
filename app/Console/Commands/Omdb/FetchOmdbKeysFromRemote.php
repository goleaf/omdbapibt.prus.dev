<?php

namespace App\Console\Commands\Omdb;

use App\Services\OmdbApiKeyManager;
use Illuminate\Console\Command;
use Throwable;

class FetchOmdbKeysFromRemote extends Command
{
    protected $signature = 'omdb:fetch-remote-keys
        {url : Remote endpoint returning candidate keys}
        {--field= : JSON field containing the candidate collection when the response is JSON}';

    protected $description = 'Import candidate OMDb API keys from a remote endpoint and persist pending records.';

    public function __construct(protected OmdbApiKeyManager $manager)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $url = (string) $this->argument('url');
        $field = $this->option('field');

        if ($url === '') {
            $this->components->error('A valid remote endpoint URL must be provided.');

            return self::FAILURE;
        }

        try {
            $result = $this->manager->importFromRemote($url, $field ?: null);
        } catch (Throwable $exception) {
            $this->components->error(sprintf('Unable to import remote keys: %s', $exception->getMessage()));

            return self::FAILURE;
        }

        if ($result['imported'] === 0) {
            $this->components->warn('No new keys were imported.');
        } else {
            $this->components->info(sprintf('Imported %d new key(s).', $result['imported']));
        }

        if ($result['skipped'] > 0) {
            $this->line(sprintf('%d candidate(s) were skipped because they already exist.', $result['skipped']));
        }

        return self::SUCCESS;
    }
}
