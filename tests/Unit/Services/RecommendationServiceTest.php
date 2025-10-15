<?php

namespace Tests\Unit\Services;

use App\Models\Genre;
use App\Models\Movie;
use App\Models\User;
use App\Models\WatchHistory;
use App\Services\Movies\RecommendationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class RecommendationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['cache.default' => 'array']);
        Cache::store('array')->clear();
    }

    public function test_recommendations_exclude_watched_titles(): void
    {
        $service = app(RecommendationService::class);

        $user = User::factory()->create();
        $watched = Movie::factory()->create();
        $candidate = Movie::factory()->create([
            'vote_average' => 8.5,
            'popularity' => 320,
        ]);

        WatchHistory::factory()->for($user)->forMovie($watched)->create([
            'watchable_id' => $watched->id,
        ]);

        $results = $service->recommendFor($user);

        $this->assertTrue($results->contains(fn (Movie $movie) => $movie->id === $candidate->id));
        $this->assertFalse($results->contains(fn (Movie $movie) => $movie->id === $watched->id));
    }

    public function test_recency_weighting_prioritizes_recent_preferences(): void
    {
        $service = app(RecommendationService::class);

        $user = User::factory()->create();

        $romance = Genre::factory()->named('Romance', 10749)->create();
        $scienceFiction = Genre::factory()->named('Science Fiction', 878)->create();

        $recentHistoryMovie = Movie::factory()->create([
            'vote_average' => 7.2,
            'popularity' => 180,
        ]);
        $recentHistoryMovie->genres()->sync([$romance->id]);

        $olderHistoryMovie = Movie::factory()->create([
            'vote_average' => 8.6,
            'popularity' => 260,
        ]);
        $olderHistoryMovie->genres()->sync([$scienceFiction->id]);

        WatchHistory::factory()->for($user)->forMovie($recentHistoryMovie)->create([
            'watched_at' => Carbon::now()->subDays(2),
        ]);

        WatchHistory::factory()->for($user)->forMovie($olderHistoryMovie)->create([
            'watched_at' => Carbon::now()->subDays(60),
        ]);

        $romanceCandidate = Movie::factory()->create([
            'vote_average' => 7.4,
            'popularity' => 210,
        ]);
        $romanceCandidate->genres()->sync([$romance->id]);

        $scienceCandidate = Movie::factory()->create([
            'vote_average' => 8.9,
            'popularity' => 320,
        ]);
        $scienceCandidate->genres()->sync([$scienceFiction->id]);

        $results = $service->recommendFor($user, 1);

        $this->assertNotEmpty($results);
        $this->assertSame($romanceCandidate->id, $results->first()->id);
    }

    public function test_rating_alignment_prefers_titles_near_user_taste(): void
    {
        $service = app(RecommendationService::class);

        $user = User::factory()->create();

        $drama = Genre::factory()->named('Drama', 18)->create();

        $historyOne = Movie::factory()->create([
            'vote_average' => 6.1,
            'popularity' => 120,
        ]);
        $historyOne->genres()->sync([$drama->id]);

        $historyTwo = Movie::factory()->create([
            'vote_average' => 5.9,
            'popularity' => 105,
        ]);
        $historyTwo->genres()->sync([$drama->id]);

        WatchHistory::factory()->for($user)->forMovie($historyOne)->create([
            'watched_at' => Carbon::now()->subDays(4),
        ]);

        WatchHistory::factory()->for($user)->forMovie($historyTwo)->create([
            'watched_at' => Carbon::now()->subDays(9),
        ]);

        $alignedCandidate = Movie::factory()->create([
            'vote_average' => 6.2,
            'popularity' => 190,
        ]);
        $alignedCandidate->genres()->sync([$drama->id]);

        $highCandidate = Movie::factory()->create([
            'vote_average' => 9.1,
            'popularity' => 240,
        ]);
        $highCandidate->genres()->sync([$drama->id]);

        $results = $service->recommendFor($user, 1);

        $this->assertSame($alignedCandidate->id, $results->first()->id);
        $this->assertNotSame($highCandidate->id, $results->first()->id);
    }

    public function test_recommendations_cache_tracks_history_signature(): void
    {
        $service = app(RecommendationService::class);

        $user = User::factory()->create();
        $genre = Genre::factory()->named('Adventure', 12)->create();

        $firstCandidate = Movie::factory()->create([
            'vote_average' => 8.8,
            'popularity' => 480,
        ]);
        $firstCandidate->genres()->sync([$genre->id]);

        $secondCandidate = Movie::factory()->create([
            'vote_average' => 8.1,
            'popularity' => 430,
        ]);
        $secondCandidate->genres()->sync([$genre->id]);

        $initial = $service->recommendFor($user, 1);
        $this->assertSame($firstCandidate->id, $initial->first()->id);

        $cacheKey = sprintf('recommendations:user:%d:%d', $user->id, 1);
        $cacheStore = Cache::store('array');
        $firstCache = $cacheStore->get($cacheKey);

        $this->assertIsArray($firstCache);
        $this->assertEquals([$firstCandidate->id], $firstCache['movie_ids']);

        WatchHistory::factory()->for($user)->forMovie($firstCandidate)->create([
            'watched_at' => Carbon::now(),
        ]);

        $refreshed = $service->recommendFor($user, 1);

        $this->assertSame($secondCandidate->id, $refreshed->first()->id);

        $updatedCache = $cacheStore->get($cacheKey);

        $this->assertEquals([$secondCandidate->id], $updatedCache['movie_ids']);
        $this->assertNotSame($firstCache['version'], $updatedCache['version']);
    }
}
