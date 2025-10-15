<?php

namespace Tests\Unit\Services;

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
        $watched = Movie::factory()->create();
        $candidate = Movie::factory()->create([
            'vote_average' => 8.5,
            'popularity' => 320,
        ]);

        WatchHistory::factory()->for($user)->forMovie()->create([
            'watchable_id' => $watched->id,
        ]);

        $results = $service->recommendFor($user);

        $this->assertTrue($results->contains(fn (Movie $movie) => $movie->id === $candidate->id));
        $this->assertFalse($results->contains(fn (Movie $movie) => $movie->id === $watched->id));
    }
}
