<?php

namespace Tests\Unit\Models;

use App\Models\Movie;
use App\Models\TvShow;
use App\Models\User;
use App\Models\WatchHistory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WatchHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_casts_watched_at_timestamp(): void
    {
        $history = WatchHistory::factory()->create([
            'watched_at' => '2024-05-01 12:00:00',
        ]);

        $this->assertSame('2024-05-01 12:00:00', $history->watched_at->format('Y-m-d H:i:s'));
    }

    public function test_scope_filters_by_user(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        $userHistory = WatchHistory::factory()->create(['user_id' => $user->id]);
        WatchHistory::factory()->create(['user_id' => $other->id]);

        $results = WatchHistory::forUser($user)->get();

        $this->assertCount(1, $results);
        $this->assertTrue($results->first()->is($userHistory));
    }

    public function test_morph_relationship_returns_watchable_model(): void
    {
        $user = User::factory()->create();
        $movie = Movie::factory()->create();
        $show = TvShow::factory()->create();

        $movieHistory = WatchHistory::factory()->forMovie($movie)->create(['user_id' => $user->id]);
        $showHistory = WatchHistory::factory()->forTvShow($show)->create(['user_id' => $user->id]);

        $this->assertTrue($movieHistory->watchable->is($movie));
        $this->assertTrue($showHistory->watchable->is($show));
    }
}
