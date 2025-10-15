<?php

namespace Tests\Unit\Services;

use App\Models\Genre;
use App\Models\Movie;
use App\Models\User;
use App\Models\WatchHistory;
use App\Services\Movies\RecommendationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecommendationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_recommendations_exclude_watched_titles(): void
    {
        $service = app(RecommendationService::class);

        $user = User::factory()->create();
        $genre = Genre::factory()->create(['slug' => 'thriller']);
        $watched = Movie::factory()->create();
        $watched->genres()->attach($genre);
        $candidate = Movie::factory()->create([
            'vote_average' => 8.5,
            'popularity' => 320,
        ]);
        $candidate->genres()->attach($genre);

        WatchHistory::factory()->for($user)->forMovie($watched)->create();

        $results = $service->recommendFor($user);

        $this->assertTrue($results->contains(fn (Movie $movie) => $movie->id === $candidate->id));
        $this->assertFalse($results->contains(fn (Movie $movie) => $movie->id === $watched->id));
    }

    public function test_preference_profile_weights_recent_history_more_heavily(): void
    {
        $service = app(RecommendationService::class);

        $user = User::factory()->create();
        $recentGenre = Genre::factory()->create(['slug' => 'sci-fi']);
        $olderGenre = Genre::factory()->create(['slug' => 'drama']);

        $recentMovie = Movie::factory()->create([
            'vote_average' => 9.0,
            'year' => 2024,
        ]);
        $recentMovie->genres()->attach($recentGenre);

        $olderMovie = Movie::factory()->create([
            'vote_average' => 5.0,
            'year' => 1990,
        ]);
        $olderMovie->genres()->attach($olderGenre);

        WatchHistory::factory()->for($user)->forMovie($recentMovie)->create([
            'watched_at' => now()->subDays(3),
        ]);

        WatchHistory::factory()->for($user)->forMovie($olderMovie)->create([
            'watched_at' => now()->subDays(60),
        ]);

        $profile = $service->buildPreferenceProfile($user);

        $this->assertSame(1.0, $profile['genres'][$recentGenre->slug]);
        $this->assertArrayHasKey($olderGenre->slug, $profile['genres']);
        $this->assertLessThan(1.0, $profile['genres'][$olderGenre->slug]);
        $this->assertGreaterThan(7.0, $profile['average_rating']);
        $this->assertNotNull($profile['release_year']);
        $this->assertGreaterThan($olderMovie->year, $profile['release_year']);
    }
}
