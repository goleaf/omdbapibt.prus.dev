<?php

namespace Tests\Unit\Services;

use App\Models\OmdbApiKey;
use App\Services\OmdbApiKeyResolver;
use Illuminate\Cache\Repository as CacheRepository;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class OmdbApiKeyResolverTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('omdb_api_keys');

        Schema::create('omdb_api_keys', function (Blueprint $table): void {
            $table->id();
            $table->string('key')->unique();
            $table->string('status')->nullable();
            $table->timestamp('last_checked_at')->nullable();
            $table->timestamp('last_confirmed_at')->nullable();
            $table->unsignedSmallInteger('last_response_code')->nullable();
            $table->timestamps();
        });

        Cache::forget('services:omdb:resolver:last_used');
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('omdb_api_keys');

        parent::tearDown();
    }

    public function test_it_returns_latest_validated_working_key(): void
    {
        config()->set('services.omdb.key', 'fallback-key');

        OmdbApiKey::query()->create([
            'key' => 'first-key',
            'status' => OmdbApiKey::STATUS_VALID,
            'last_checked_at' => now()->subDay(),
            'last_confirmed_at' => now()->subDay(),
        ]);

        OmdbApiKey::query()->create([
            'key' => 'second-key',
            'status' => OmdbApiKey::STATUS_VALID,
            'last_checked_at' => now(),
            'last_confirmed_at' => now(),
        ]);

        $resolver = $this->makeResolver();

        $this->assertSame('second-key', $resolver->resolve());
    }

    public function test_it_falls_back_to_config_when_no_records_exist(): void
    {
        config()->set('services.omdb.key', 'fallback-key');

        $resolver = $this->makeResolver();

        $this->assertSame('fallback-key', $resolver->resolve());
    }

    public function test_it_ignores_non_working_keys(): void
    {
        config()->set('services.omdb.key', 'fallback-key');

        OmdbApiKey::query()->create([
            'key' => 'failing-key',
            'status' => OmdbApiKey::STATUS_INVALID,
            'last_checked_at' => now(),
            'last_confirmed_at' => null,
        ]);

        OmdbApiKey::query()->create([
            'key' => 'working-key',
            'status' => OmdbApiKey::STATUS_VALID,
            'last_checked_at' => now()->subMinute(),
            'last_confirmed_at' => now()->subMinute(),
        ]);

        $resolver = $this->makeResolver();

        $this->assertSame('working-key', $resolver->resolve());
    }

    public function test_it_falls_back_when_table_is_missing(): void
    {
        config()->set('services.omdb.key', 'fallback-key');

        Schema::drop('omdb_api_keys');

        $resolver = $this->makeResolver();

        $this->assertSame('fallback-key', $resolver->resolve());
    }

    public function test_it_rotates_through_valid_keys(): void
    {
        config()->set('services.omdb.key', 'fallback-key');

        OmdbApiKey::query()->create([
            'key' => 'key-one',
            'status' => OmdbApiKey::STATUS_VALID,
            'last_checked_at' => now(),
            'last_confirmed_at' => now(),
        ]);

        OmdbApiKey::query()->create([
            'key' => 'key-two',
            'status' => OmdbApiKey::STATUS_VALID,
            'last_checked_at' => now()->subMinute(),
            'last_confirmed_at' => now()->subMinute(),
        ]);

        $resolver = $this->makeResolver();

        $this->assertSame('key-one', $resolver->resolve());
        $this->assertSame('key-two', $resolver->resolve());
        $this->assertSame('key-one', $resolver->resolve());
    }

    public function test_it_uses_fallback_when_valid_keys_are_stale(): void
    {
        config()->set('services.omdb.key', 'fallback-key');
        config()->set('services.omdb.validation.health_grace_minutes', 5);

        OmdbApiKey::query()->create([
            'key' => 'stale-key',
            'status' => OmdbApiKey::STATUS_VALID,
            'last_confirmed_at' => now()->subMinutes(10),
        ]);

        $resolver = $this->makeResolver();

        $this->assertSame('fallback-key', $resolver->resolve());
    }

    protected function makeResolver(): OmdbApiKeyResolver
    {
        return new OmdbApiKeyResolver(app(CacheRepository::class));
    }
}
