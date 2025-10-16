<?php

namespace Tests\Feature\Console;

use App\Services\OmdbApiKeyManager;
use Illuminate\Cache\Repository as CacheRepository;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Factory as HttpFactory;
use Tests\TestCase;

class ParseMoviesWithApiKeysCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_invokes_manager_with_cli_overrides(): void
    {
        $manager = new class(
            app(HttpFactory::class),
            app(CacheRepository::class),
            app(ConnectionInterface::class)
        ) extends OmdbApiKeyManager {
            public array $calledWith = [];

            public function parseMoviesWithKeys(int $movieLimit, int $chunkSize, string $baseUrl, int $timeout): array
            {
                $this->calledWith = [$movieLimit, $chunkSize, $baseUrl, $timeout];

                return [
                    'processed' => 5,
                    'updated' => 3,
                    'status' => 'success',
                    'key_count' => 2,
                    'candidates' => 5,
                ];
            }
        };

        app()->instance(OmdbApiKeyManager::class, $manager);

        $this->artisan('omdb:parse-movies', [
            '--limit' => 25,
            '--chunk' => 5,
            '--timeout' => 12,
            '--base-url' => 'https://omdb.test/api',
        ])
            ->expectsOutputToContain('Parsing up to 25 movie(s) with batches of 5 requests.')
            ->expectsOutputToContain('Processed 5 movie(s) and updated 3 record(s).')
            ->assertSuccessful();

        $this->assertSame([25, 5, 'https://omdb.test/api', 12], $manager->calledWith);
    }

    public function test_command_warns_when_no_movies_processed(): void
    {
        $manager = new class(
            app(HttpFactory::class),
            app(CacheRepository::class),
            app(ConnectionInterface::class)
        ) extends OmdbApiKeyManager {
            public function parseMoviesWithKeys(int $movieLimit, int $chunkSize, string $baseUrl, int $timeout): array
            {
                return [
                    'processed' => 0,
                    'updated' => 0,
                    'status' => 'no_keys',
                    'key_count' => 0,
                    'candidates' => 0,
                ];
            }
        };

        app()->instance(OmdbApiKeyManager::class, $manager);

        $this->artisan('omdb:parse-movies')
            ->expectsOutputToContain('No valid OMDb keys are available.')
            ->assertSuccessful();
    }
}
