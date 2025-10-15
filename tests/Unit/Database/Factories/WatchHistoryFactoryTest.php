<?php

namespace Tests\Unit\Database\Factories;

use App\Models\Movie;
use App\Models\TvShow;
use App\Models\WatchHistory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WatchHistoryFactoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_state_uses_movie_watchable(): void
    {
        $history = WatchHistory::factory()->create();

        $this->assertInstanceOf(Movie::class, $history->watchable);
    }

    public function test_for_tv_show_state_sets_tv_show_watchable(): void
    {
        $history = WatchHistory::factory()->forTvShow()->create();

        $this->assertInstanceOf(TvShow::class, $history->watchable);
    }

    public function test_for_watchable_uses_provided_model(): void
    {
        $movie = Movie::factory()->create();

        $history = WatchHistory::factory()->forWatchable($movie)->create();

        $this->assertTrue($movie->is($history->watchable));
    }
}
