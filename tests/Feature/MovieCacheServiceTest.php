<?php

namespace Tests\Feature;

use App\Models\Movie;
use App\Services\Movies\MovieCacheService;
use App\Services\Movies\ParsedMoviePersister;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class MovieCacheServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('cache.default', 'array');
        Carbon::setTestNow(Carbon::parse('2024-01-01 00:00:00'));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_trending_results_are_cached_for_one_hour(): void
    {
        Movie::factory()->count(3)->create();
        $service = app(MovieCacheService::class);

        $firstResult = $service->trending(1)->first();

        Carbon::setTestNow(Carbon::now()->addMinutes(10));
        $newMovie = Movie::factory()->create(['popularity' => 999]);

        $secondResult = $service->trending(1)->first();
        $this->assertTrue($secondResult->is($firstResult));

        Carbon::setTestNow(Carbon::now()->addHour()->addSecond());

        $thirdResult = $service->trending(1)->first();
        $this->assertTrue($thirdResult->is($newMovie));
    }

    public function test_popular_results_are_cached_for_one_hour(): void
    {
        $movie = Movie::factory()->create(['popularity' => 100]);
        $service = app(MovieCacheService::class);

        $first = $service->popular(1)->first();
        $this->assertTrue($first->is($movie));

        $movie->update(['popularity' => 10]);

        Carbon::setTestNow(Carbon::now()->addMinutes(10));
        $newTop = Movie::factory()->create(['popularity' => 999]);

        $second = $service->popular(1)->first();
        $this->assertTrue($second->is($movie));

        Carbon::setTestNow(Carbon::now()->addHour()->addSecond());

        $third = $service->popular(1)->first();
        $this->assertTrue($third->is($newTop));
    }

    public function test_persister_skips_cache_and_invalidates_tags(): void
    {
        $movie = Movie::factory()->create([
            'tmdb_id' => 42,
            'title' => 'Original',
            'popularity' => 10,
        ]);

        $cacheService = app(MovieCacheService::class);
        $persister = app(ParsedMoviePersister::class);

        $cached = $cacheService->popular(1)->first();
        $this->assertTrue($cached->is($movie));

        $persister->persist([
            'tmdb_id' => 42,
            'title' => 'Original',
            'popularity' => 500,
        ]);

        $fresh = $cacheService->popular(1)->first();
        $this->assertSame(500.0, $fresh->popularity);
    }

    public function test_persister_requires_identifier(): void
    {
        $persister = app(ParsedMoviePersister::class);

        $this->expectException(\InvalidArgumentException::class);

        $persister->persist([
            'title' => 'Missing identifier',
        ]);
    }
}
