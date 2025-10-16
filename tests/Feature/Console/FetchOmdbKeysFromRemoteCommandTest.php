<?php

namespace Tests\Feature\Console;

use App\Models\OmdbApiKey;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class FetchOmdbKeysFromRemoteCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_imports_remote_keys_and_reports_duplicates(): void
    {
        OmdbApiKey::factory()->create(['key' => 'existing', 'status' => OmdbApiKey::STATUS_PENDING]);

        Http::fake(['https://remote.test/keys' => Http::response("new1\nexisting\nnew2", 200)]);

        $this->artisan('omdb:fetch-remote-keys', ['url' => 'https://remote.test/keys'])
            ->expectsOutputToContain('Imported 2 new key(s).')
            ->expectsOutputToContain('1 candidate(s) were skipped because they already exist.')
            ->assertSuccessful();

        $this->assertEqualsCanonicalizing(['existing', 'new1', 'new2'], OmdbApiKey::pluck('key')->all());
    }

    public function test_command_handles_http_errors(): void
    {
        Http::fake(['https://remote.test/keys' => Http::response('', 500)]);

        $this->artisan('omdb:fetch-remote-keys', ['url' => 'https://remote.test/keys'])
            ->expectsOutputToContain('Unable to import remote keys:')
            ->assertFailed();
    }
}
