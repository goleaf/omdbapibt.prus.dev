<?php

namespace Tests\Feature;

use App\Models\Genre;
use App\Models\Movie;
use App\Models\User;
use App\Models\WatchHistory;
use App\Services\Movies\RecommendationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class RecommendationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('cache.default', 'array');
        config()->set('cache_ttls.queries.recommendations', 60);
        Carbon::setTestNow(Carbon::parse('2025-01-01 00:00:00'));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_recommendations_prioritize_user_preferences(): void
    {
        $user = User::factory()->create();
        $action = Genre::create(['name' => 'Action', 'slug' => 'action', 'tmdb_id' => 28]);
        $drama = Genre::create(['name' => 'Drama', 'slug' => 'drama', 'tmdb_id' => 18]);

        $watched = Movie::factory()->create([
            'vote_average' => 6.0,
            'popularity' => 30,
        ]);
        $watched->genres()->attach($action);

        WatchHistory::factory()->create([
            'user_id' => $user->getKey(),
            'movie_id' => $watched->getKey(),
            'rewatch_count' => 4,
        ]);

        $preferred = Movie::factory()->create([
            'vote_average' => 7.2,
            'popularity' => 45,
        ]);
        $preferred->genres()->attach($action);

        $other = Movie::factory()->create([
            'vote_average' => 9.5,
            'popularity' => 90,
        ]);
        $other->genres()->attach($drama);

        $service = app(RecommendationService::class);
        $results = $service->forUser($user, 2);

        $this->assertTrue($results->first()->is($preferred));
        $this->assertTrue($results->contains($other));
    }

    public function test_recommendations_are_cached_until_expired(): void
    {
        $user = User::factory()->create();
        $genre = Genre::create(['name' => 'Sci-Fi', 'slug' => 'sci-fi', 'tmdb_id' => 878]);

        $watched = Movie::factory()->create(['vote_average' => 8.0, 'popularity' => 50]);
        $watched->genres()->attach($genre);

        WatchHistory::factory()->create([
            'user_id' => $user->getKey(),
            'movie_id' => $watched->getKey(),
            'rewatch_count' => 2,
        ]);

        $initial = Movie::factory()->create(['vote_average' => 7.5, 'popularity' => 40]);
        $initial->genres()->attach($genre);

        $service = app(RecommendationService::class);
        $firstResult = $service->forUser($user, 1)->first();
        $this->assertTrue($firstResult->is($initial));

        $improved = Movie::factory()->create(['vote_average' => 9.8, 'popularity' => 80]);
        $improved->genres()->attach($genre);

        $secondResult = $service->forUser($user, 1)->first();
        $this->assertTrue($secondResult->is($initial));

        Carbon::setTestNow(Carbon::now()->addSeconds(61));
        $thirdResult = $service->forUser($user, 1)->first();
        $this->assertTrue($thirdResult->is($improved));
    }
}
