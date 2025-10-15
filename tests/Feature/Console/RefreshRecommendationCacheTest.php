<?php

namespace Tests\Feature\Console;

use App\Models\Genre;
use App\Models\Movie;
use App\Models\User;
use App\Models\WatchHistory;
use App\Services\Movies\RecommendationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class RefreshRecommendationCacheTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['cache.default' => 'array']);
        Cache::store('array')->clear();
    }

    public function test_command_rebuilds_recommendations_for_specified_users(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->named('Adventure', 12)->create();

        $primary = Movie::factory()->create([
            'vote_average' => 8.7,
            'popularity' => 520,
        ]);
        $primary->genres()->sync([$genre->id]);

        $secondary = Movie::factory()->create([
            'vote_average' => 8.2,
            'popularity' => 470,
        ]);
        $secondary->genres()->sync([$genre->id]);

        $service = app(RecommendationService::class);
        $initial = $service->recommendFor($user, 1);

        $this->assertSame($primary->id, $initial->first()->id);

        WatchHistory::factory()->for($user)->forMovie($primary)->create([
            'watched_at' => Carbon::now(),
        ]);

        $this->artisan('recommendations:refresh', ['--user' => [$user->id]])
            ->expectsOutput('Refreshed recommendations for 1 users.')
            ->assertExitCode(0);

        $cacheKey = sprintf('recommendations:user:%d:%d', $user->id, 1);
        $cached = Cache::store('array')->get($cacheKey);

        $this->assertEquals([$secondary->id], $cached['movie_ids']);

        $refreshed = $service->recommendFor($user, 1);

        $this->assertSame($secondary->id, $refreshed->first()->id);
    }
}
