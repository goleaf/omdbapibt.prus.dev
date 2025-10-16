<?php

namespace App\Console\Commands\Omdb;

use App\Services\OmdbApiKeyManager;
use Illuminate\Console\Command;

class ParseMoviesWithApiKeys extends Command
{
    protected $signature = 'omdb:parse-movies
        {--limit= : Maximum number of movies to evaluate per execution}
        {--chunk= : Number of concurrent requests per batch}
        {--timeout= : Timeout in seconds applied to OMDb requests}
        {--base-url= : Override the OMDb base URL}';

    protected $description = 'Parse movies using confirmed OMDb API keys and refresh metadata.';

    public function __construct(protected OmdbApiKeyManager $manager)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $limit = max(1, (int) ($this->option('limit') ?? 1000));
        $chunk = max(1, (int) ($this->option('chunk') ?? 50));
        $timeout = max(1, (int) ($this->option('timeout') ?? config('services.omdb.validation.timeout', 10)));
        $baseUrl = rtrim((string) ($this->option('base-url') ?? config('services.omdb.base_url', 'https://www.omdbapi.com/')), '/');

        $this->components->info(sprintf(
            'Parsing up to %d movie(s) with batches of %d requests.',
            $limit,
            $chunk
        ));

        $result = $this->manager->parseMoviesWithKeys($limit, $chunk, $baseUrl, $timeout);

        if ($result['status'] === 'no_keys') {
            $this->components->warn('No valid OMDb keys are available.');

            return self::SUCCESS;
        }

        if ($result['status'] === 'no_movies') {
            $this->components->info('No eligible movies were found to update.');

            return self::SUCCESS;
        }

        $this->components->info(sprintf(
            'Processed %d movie(s) and updated %d record(s).',
            $result['processed'],
            $result['updated']
        ));

        return self::SUCCESS;
    }
}
