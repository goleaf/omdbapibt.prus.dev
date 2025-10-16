<?php

namespace Tests\Feature\Console;

use App\Models\OmdbApiKey;
use App\Services\OmdbApiKeyManager;
use Illuminate\Cache\Repository as CacheRepository;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Factory as HttpFactory;
use Tests\TestCase;

class GenerateOmdbKeysCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_generates_required_candidates_using_cli_overrides(): void
    {
        OmdbApiKey::factory()->count(2)->pending()->create();

        app()->bind(OmdbApiKeyManager::class, function ($app) {
            return new class(
                $app->make(HttpFactory::class),
                $app->make(CacheRepository::class),
                $app->make(ConnectionInterface::class)
            ) extends OmdbApiKeyManager {
                protected int $counter = 0;

                protected function generateKey(string $charset, int $length): string
                {
                    return 'stub'.str_pad((string) $this->counter++, $length, '0', STR_PAD_LEFT);
                }
            };
        });

        $this->artisan('omdb:generate-keys', [
            '--minimum' => 5,
            '--batch' => 2,
            '--length' => 4,
            '--charset' => '0123456789',
        ])
            ->expectsOutputToContain('Ensuring at least 5 pending OMDb key candidates.')
            ->expectsOutputToContain('Generated 3 new candidate(s).')
            ->assertSuccessful();

        $this->assertSame(5, OmdbApiKey::pending()->count());
    }

    public function test_command_fails_when_invalid_minimum_provided(): void
    {
        $this->artisan('omdb:generate-keys', ['--minimum' => 0])
            ->expectsOutputToContain('Minimum pending keys must be greater than zero.')
            ->assertFailed();
    }
}
