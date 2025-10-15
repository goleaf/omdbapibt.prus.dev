<?php

namespace Tests\Feature\Livewire;

use App\Livewire\DashboardRecommendations;
use App\Models\Genre;
use App\Models\Movie;
use App\Models\User;
use App\Models\WatchHistory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardRecommendationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_component_displays_personalized_movies(): void
    {
        config()->set('cache.default', 'array');
        config()->set('cache_ttls.queries.recommendations', 60);

        $user = User::factory()->create();
        $genre = Genre::create(['name' => 'Fantasy', 'slug' => 'fantasy', 'tmdb_id' => 14]);

        $watched = Movie::factory()->create(['vote_average' => 7.0, 'popularity' => 20]);
        $watched->genres()->attach($genre);

        WatchHistory::factory()->create([
            'user_id' => $user->getKey(),
            'movie_id' => $watched->getKey(),
        ]);

        $recommended = Movie::factory()->create(['title' => 'Arcane Tales', 'vote_average' => 8.4, 'popularity' => 55]);
        $recommended->genres()->attach($genre);

        $this->actingAs($user);

        Livewire::test(DashboardRecommendations::class)
            ->assertSee('Personalized suggestions')
            ->assertSee('Arcane Tales');
    }

    public function test_component_shows_empty_state_for_new_users(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test(DashboardRecommendations::class)
            ->assertSee('Personalized suggestions')
            ->assertSee('Start watching movies');
    }
}
