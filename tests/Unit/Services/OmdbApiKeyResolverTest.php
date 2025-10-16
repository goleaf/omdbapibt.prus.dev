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
            $table->unsignedSmallInteger('consecutive_failures')->default(0);
            $table->timestamp('disabled_until')->nullable();
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
            'consecutive_failures' => 0,
        ]);

        OmdbApiKey::query()->create([
            'key' => 'second-key',
            'status' => OmdbApiKey::STATUS_VALID,
            'last_checked_at' => now(),
            'last_confirmed_at' => now(),
            'consecutive_failures' => 0,
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
            'consecutive_failures' => 0,
        ]);

        OmdbApiKey::query()->create([
            'key' => 'working-key',
            'status' => OmdbApiKey::STATUS_VALID,
            'last_checked_at' => now()->subMinute(),
            'last_confirmed_at' => now()->subMinute(),
            'consecutive_failures' => 0,
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
            'consecutive_failures' => 0,
        ]);

        OmdbApiKey::query()->create([
            'key' => 'key-two',
            'status' => OmdbApiKey::STATUS_VALID,
            'last_checked_at' => now()->subMinute(),
            'last_confirmed_at' => now()->subMinute(),
            'consecutive_failures' => 0,
        ]);

        $resolver = $this->makeResolver();

        $this->assertSame('key-one', $resolver->resolve());
        $this->assertSame('key-two', $resolver->resolve());
        $this->assertSame('key-one', $resolver->resolve());
    }

    public function test_cursor_persists_across_instances(): void
    {
        config()->set('services.omdb.key', 'fallback-key');

        OmdbApiKey::query()->create([
            'key' => 'alpha',
            'status' => OmdbApiKey::STATUS_VALID,
            'last_confirmed_at' => now(),
            'consecutive_failures' => 0,
        ]);

        OmdbApiKey::query()->create([
            'key' => 'bravo',
            'status' => OmdbApiKey::STATUS_VALID,
            'last_confirmed_at' => now()->subMinute(),
            'consecutive_failures' => 0,
        ]);

        $resolver = $this->makeResolver();
        $this->assertSame('alpha', $resolver->resolve());

        // New resolver instance should pick up rotation from cache.
        $resolver = $this->makeResolver();
        $this->assertSame('bravo', $resolver->resolve());

        $resolver = $this->makeResolver();
        $this->assertSame('alpha', $resolver->resolve());
    }

    public function test_report_failure_temporarily_disables_key(): void
    {
        config()->set('services.omdb.key', 'fallback-key');
        config()->set('services.omdb.validation.failure_threshold', 2);
        config()->set('services.omdb.validation.failure_backoff_minutes', 10);

        OmdbApiKey::query()->create([
            'key' => 'flaky',
            'status' => OmdbApiKey::STATUS_VALID,
            'last_confirmed_at' => now(),
            'consecutive_failures' => 0,
        ]);

        OmdbApiKey::query()->create([
            'key' => 'stable',
            'status' => OmdbApiKey::STATUS_VALID,
            'last_confirmed_at' => now()->subMinute(),
            'consecutive_failures' => 0,
        ]);

        $resolver = $this->makeResolver();

        $this->assertSame('flaky', $resolver->resolve());

        $resolver->reportFailure('flaky', 500);

        // After first failure it should rotate but not disable yet.
        $this->assertSame('stable', $resolver->resolve());

        $resolver->reportFailure('flaky', 500);

        $health = $resolver->health('flaky');

        $this->assertArrayHasKey('flaky', $health);
        $this->assertSame(2, $health['flaky']['consecutive_failures']);
        $this->assertSame(OmdbApiKey::STATUS_UNKNOWN, $health['flaky']['status']);
        $this->assertNotNull($health['flaky']['disabled_until']);

        // Resolver should skip temporarily disabled key and continue rotation.
        $this->assertSame('stable', $resolver->resolve());
    }

    public function test_report_success_resets_failure_state(): void
    {
        config()->set('services.omdb.key', 'fallback-key');

        $key = OmdbApiKey::query()->create([
            'key' => 'recovered',
            'status' => OmdbApiKey::STATUS_UNKNOWN,
            'consecutive_failures' => 3,
            'disabled_until' => now()->addHour(),
        ]);

        $resolver = $this->makeResolver();

        $resolver->reportSuccess('recovered', 200);

        $key->refresh();

        $this->assertSame(0, $key->consecutive_failures);
        $this->assertNull($key->disabled_until);
        $this->assertSame(OmdbApiKey::STATUS_VALID, $key->status);
        $this->assertSame(200, $key->last_response_code);
        $this->assertNotNull($key->last_confirmed_at);
    }

    public function test_reset_key_clears_failure_state_and_marks_pending_when_requested(): void
    {
        config()->set('services.omdb.key', 'fallback-key');

        $key = OmdbApiKey::query()->create([
            'key' => 'retry',
            'status' => OmdbApiKey::STATUS_UNKNOWN,
            'consecutive_failures' => 4,
            'disabled_until' => now()->addHour(),
        ]);

        $resolver = $this->makeResolver();

        $resolver->resetKey('retry', markPending: true);

        $key->refresh();

        $this->assertSame(0, $key->consecutive_failures);
        $this->assertNull($key->disabled_until);
        $this->assertSame(OmdbApiKey::STATUS_PENDING, $key->status);
    }

    public function test_it_uses_fallback_when_valid_keys_are_stale(): void
    {
        config()->set('services.omdb.key', 'fallback-key');
        config()->set('services.omdb.validation.health_grace_minutes', 5);

        OmdbApiKey::query()->create([
            'key' => 'stale-key',
            'status' => OmdbApiKey::STATUS_VALID,
            'last_confirmed_at' => now()->subMinutes(10),
            'consecutive_failures' => 0,
        ]);

        $resolver = $this->makeResolver();

        $this->assertSame('fallback-key', $resolver->resolve());
    }

    protected function makeResolver(): OmdbApiKeyResolver
    {
        return new OmdbApiKeyResolver(app(CacheRepository::class));
    }
}
