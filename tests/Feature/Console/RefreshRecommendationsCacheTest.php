<?php

namespace Tests\Feature\Console;

use App\Models\Genre;
use App\Models\Movie;
use App\Models\User;
use App\Models\WatchHistory;
use App\Services\Movies\RecommendationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RefreshRecommendationsCacheTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_refreshes_cached_recommendations(): void
    {
        config()->set('cache.default', 'array');
        config()->set('cache_ttls.queries.recommendations', 3600);

        $user = User::factory()->create();
        $genre = Genre::create(['name' => 'Thriller', 'slug' => 'thriller', 'tmdb_id' => 53]);

        $watched = Movie::factory()->create(['vote_average' => 6.5, 'popularity' => 25]);
        $watched->genres()->attach($genre);

        WatchHistory::factory()->create([
            'user_id' => $user->getKey(),
            'movie_id' => $watched->getKey(),
        ]);

        $initial = Movie::factory()->create(['title' => 'Existing Pick', 'vote_average' => 7.0, 'popularity' => 30]);
        $initial->genres()->attach($genre);

        $service = app(RecommendationService::class);
        $this->assertTrue($service->forUser($user, 1)->first()->is($initial));

        $improved = Movie::factory()->create(['title' => 'Fresh Thriller', 'vote_average' => 9.4, 'popularity' => 80]);
        $improved->genres()->attach($genre);

        $this->assertTrue($service->forUser($user, 1)->first()->is($initial));

        $this->artisan('recommendations:refresh', ['--limit' => 1])
            ->expectsOutput('Refreshed recommendations for 1 user(s).')
            ->assertSuccessful();

        $this->assertTrue($service->forUser($user, 1)->first()->is($improved));
    }
}
