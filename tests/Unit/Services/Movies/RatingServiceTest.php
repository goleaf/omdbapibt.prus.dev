<?php

namespace Tests\Unit\Services\Movies;

use App\Models\Movie;
use App\Models\Rating;
use App\Models\User;
use App\Services\Movies\RatingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RatingServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_submit_score_creates_or_updates_rating(): void
    {
        $service = $this->app->make(RatingService::class);

        $user = User::factory()->create();
        $movie = Movie::factory()->create();

        $created = $service->submitScore($user, $movie, 7);

        $this->assertDatabaseHas('ratings', [
            'user_id' => $user->id,
            'movie_id' => $movie->id,
            'rating' => 7,
        ]);

        $this->assertSame(7, $created->rating);
        $this->assertSame(1, Rating::query()->count());

        $updated = $service->submitScore($user, $movie, 4);

        $this->assertSame(4, $updated->rating);
        $this->assertSame(1, Rating::query()->count());
    }

    public function test_toggle_like_sets_like_and_clears_dislike(): void
    {
        $service = $this->app->make(RatingService::class);

        $user = User::factory()->create();
        $movie = Movie::factory()->create();

        $rating = $service->toggleLike($user, $movie);

        $this->assertTrue($rating->liked);
        $this->assertFalse($rating->disliked);

        $rating = $service->toggleDislike($user, $movie);

        $this->assertTrue($rating->disliked);
        $this->assertFalse($rating->liked);

        $rating = $service->toggleLike($user, $movie);

        $this->assertTrue($rating->liked);
        $this->assertFalse($rating->disliked);

        $rating = $service->toggleLike($user, $movie);

        $this->assertFalse($rating->liked);
    }

    public function test_find_for_user_returns_existing_rating(): void
    {
        $service = $this->app->make(RatingService::class);

        $user = User::factory()->create();
        $movie = Movie::factory()->create();

        $this->assertNull($service->findForUser($user, $movie));

        $service->submitScore($user, $movie, 6);

        $this->assertNotNull($service->findForUser($user, $movie));
    }
}
