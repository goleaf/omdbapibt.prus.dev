<?php

namespace Tests\Feature\Livewire;

use App\Livewire\MovieDetail;
use App\Models\Movie;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MovieDetailRatingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_submit_rating_and_reactions(): void
    {
        $user = User::factory()->create();
        $movie = Movie::factory()->create();

        Livewire::actingAs($user)
            ->test(MovieDetail::class, ['movie' => (string) $movie->id])
            ->call('submitRating', 8)
            ->assertSet('userRating', 8)
            ->assertSet('userLiked', false)
            ->assertSet('userDisliked', false)
            ->call('toggleLike')
            ->assertSet('userLiked', true)
            ->assertSet('userDisliked', false)
            ->call('toggleDislike')
            ->assertSet('userLiked', false)
            ->assertSet('userDisliked', true);

        $this->assertDatabaseHas('ratings', [
            'user_id' => $user->id,
            'movie_id' => $movie->id,
            'rating' => 8,
            'liked' => false,
            'disliked' => true,
        ]);
    }
}
