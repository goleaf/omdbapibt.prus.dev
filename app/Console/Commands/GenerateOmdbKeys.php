<?php

namespace App\Console\Commands;

use App\Services\OmdbApiKeyManager;
use Illuminate\Console\Command;

class GenerateOmdbKeys extends Command
{
    protected $signature = 'omdb:generate-keys
        {--minimum= : Minimum pending keys to maintain}
        {--batch= : Number of candidates generated per insert batch}
        {--length= : Length of generated keys}
        {--charset= : Character set used when generating keys}';

    protected $description = 'Generate OMDb API key candidates until the pending pool meets the configured minimum.';

    public function __construct(protected OmdbApiKeyManager $manager)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $minimum = (int) ($this->option('minimum') ?? config('services.omdb.bruteforce.min_pending_keys', 10000));
        $batch = max(1, (int) ($this->option('batch') ?? config('services.omdb.bruteforce.generation_batch', 1000)));
        $length = max(1, (int) ($this->option('length') ?? config('services.omdb.bruteforce.key_length', 8)));
        $charset = (string) ($this->option('charset') ?? config('services.omdb.bruteforce.charset', '0123456789abcdefghijklmnopqrstuvwxyz'));

        if ($minimum <= 0) {
            $this->components->error('Minimum pending keys must be greater than zero.');

            return self::FAILURE;
        }

        $this->components->info(sprintf(
            'Ensuring at least %d pending OMDb key candidates.',
            $minimum
        ));

        $generated = $this->manager->generatePendingKeys($minimum, $charset, $length, $batch);

        if ($generated === 0) {
            $this->components->info('No new candidates were required. Pending pool already satisfied.');

            return self::SUCCESS;
        }

        $this->components->info(sprintf('Generated %d new candidate(s).', $generated));

        return self::SUCCESS;
    }
}
