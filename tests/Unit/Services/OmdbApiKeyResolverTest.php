<?php

namespace Tests\Unit\Services;

use App\Models\OmdbApiKey;
use App\Services\OmdbApiKeyResolver;
use Illuminate\Database\Schema\Blueprint;
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
            $table->boolean('is_working')->default(false);
            $table->timestamp('validated_at')->nullable();
            $table->timestamps();
        });
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
            'is_working' => true,
            'validated_at' => now()->subDay(),
        ]);

        OmdbApiKey::query()->create([
            'key' => 'second-key',
            'is_working' => true,
            'validated_at' => now(),
        ]);

        $resolver = new OmdbApiKeyResolver;

        $this->assertSame('second-key', $resolver->resolve());
    }

    public function test_it_falls_back_to_config_when_no_records_exist(): void
    {
        config()->set('services.omdb.key', 'fallback-key');

        $resolver = new OmdbApiKeyResolver;

        $this->assertSame('fallback-key', $resolver->resolve());
    }

    public function test_it_ignores_non_working_keys(): void
    {
        config()->set('services.omdb.key', 'fallback-key');

        OmdbApiKey::query()->create([
            'key' => 'failing-key',
            'is_working' => false,
            'validated_at' => now(),
        ]);

        OmdbApiKey::query()->create([
            'key' => 'working-key',
            'is_working' => true,
            'validated_at' => now()->subMinute(),
        ]);

        $resolver = new OmdbApiKeyResolver;

        $this->assertSame('working-key', $resolver->resolve());
    }

    public function test_it_falls_back_when_table_is_missing(): void
    {
        config()->set('services.omdb.key', 'fallback-key');

        Schema::drop('omdb_api_keys');

        $resolver = new OmdbApiKeyResolver;

        $this->assertSame('fallback-key', $resolver->resolve());
    }
}
