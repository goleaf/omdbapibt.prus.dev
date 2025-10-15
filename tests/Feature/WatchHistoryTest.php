<?php

namespace Tests\Feature;

use App\Models\Movie;
use App\Models\User;
use App\Models\WatchHistory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WatchHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_watch_history_relationships_are_available(): void
    {
        $history = WatchHistory::factory()->create();
        $user = $history->user;
        $movie = $history->movie;

        $this->assertTrue($user->watchHistories->contains($history));
        $this->assertTrue($user->watchedMovies->contains($movie));
        $this->assertTrue($movie->watchHistories->contains($history));
        $this->assertTrue($movie->viewers->contains($user));
    }

    public function test_history_can_be_created_for_existing_models(): void
    {
        $user = User::factory()->create();
        $movie = Movie::factory()->create();

        $history = WatchHistory::factory()->create([
            'user_id' => $user->getKey(),
            'movie_id' => $movie->getKey(),
            'rewatch_count' => 3,
        ]);

        $this->assertSame(3, $history->refresh()->rewatch_count);
        $this->assertNotNull($history->watched_at);
    }
}
