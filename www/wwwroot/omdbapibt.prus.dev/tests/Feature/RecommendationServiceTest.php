<?php

namespace Tests\Feature;

use App\Models\Movie;
use App\Models\User;
use App\Models\WatchHistory;
use App\Services\RecommendationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Date;
use Tests\TestCase;

class RecommendationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['cache.default' => 'array']);
        Cache::flush();
    }

    protected function tearDown(): void
    {
        Date::setTestNow();

        parent::tearDown();
    }

    public function test_it_excludes_movies_the_user_has_already_watched(): void
    {
        Date::setTestNow('2025-10-15 12:00:00');

        $user = User::factory()->create();
        $watchedMovies = Movie::factory()->count(2)->sequence(
            ['media_type' => 'movie', 'popularity' => 100, 'vote_average' => 7.5],
            ['media_type' => 'movie', 'popularity' => 80, 'vote_average' => 6.5],
        )->create();

        $candidate = Movie::factory()->create([
            'media_type' => 'movie',
            'popularity' => 200,
            'vote_average' => 8.0,
            'year' => 2024,
        ]);

        foreach ($watchedMovies as $movie) {
            WatchHistory::factory()
                ->for($user)
                ->for($movie)
                ->create([
                    'watched_at' => now()->subDays(10),
                    'progress' => 100,
                    'user_rating' => 8,
                ]);
        }

        $service = new RecommendationService();

        $payload = $service->getRecommendationsFor($user, 5);

        $this->assertNotEmpty($payload['profile']['watched_ids']);
        $this->assertEquals(
            [$candidate->id],
            collect($payload['items'])->pluck('id')->all()
        );
        $this->assertSame('2025-10-15T12:00:00+00:00', $payload['generated_at']);
    }

    public function test_refresh_recomputes_cached_recommendations(): void
    {
        Date::setTestNow('2025-10-15 13:00:00');

        $user = User::factory()->create();
        $initial = Movie::factory()->create([
            'popularity' => 50,
            'vote_average' => 6.0,
        ]);

        $service = new RecommendationService();

        $firstPayload = $service->getRecommendationsFor($user, 1);
        $this->assertEquals([$initial->id], collect($firstPayload['items'])->pluck('id')->all());

        Date::setTestNow('2025-10-15 14:00:00');

        $improved = Movie::factory()->create([
            'popularity' => 500,
            'vote_average' => 9.0,
        ]);

        $secondPayload = $service->getRecommendationsFor($user, 1);
        $this->assertEquals([$initial->id], collect($secondPayload['items'])->pluck('id')->all(), 'Cached payload should be returned.');

        $refreshedPayload = $service->refreshRecommendations($user, 1);
        $this->assertEquals([$improved->id], collect($refreshedPayload['items'])->pluck('id')->all());
        $this->assertSame('2025-10-15T14:00:00+00:00', $refreshedPayload['generated_at']);
    }
}
