<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Dashboard\Recommendations;
use App\Models\Genre;
use App\Models\Movie;
use App\Models\User;
use App\Services\Movies\RecommendationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardRecommendationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_component_loads_recommendations_for_authenticated_user(): void
    {
        $user = User::factory()->create();

        $genres = Genre::factory()->count(2)->create();
        $movies = Movie::factory()->count(2)->create([
            'vote_average' => 8.5,
            'popularity' => 320,
        ]);

        foreach ($movies as $index => $movie) {
            $movie->genres()->attach($genres[$index % $genres->count()]);
        }

        $this->actingAs($user);

        $service = $this->mock(RecommendationService::class);
        $service->shouldReceive('recommendFor')
            ->twice()
            ->withArgs(fn (User $passedUser) => $passedUser->is($user))
            ->andReturn(collect($movies));
        $service->shouldReceive('flush')
            ->once()
            ->withArgs(fn (User $passedUser) => $passedUser->is($user));

        Livewire::test(Recommendations::class)
            ->assertSet('isHydrated', true)
            ->assertSet('items.0.id', $movies[0]->getKey())
            ->call('refreshRecommendations')
            ->assertDispatched('recommendations-refreshed');
    }

    public function test_component_handles_guests_gracefully(): void
    {
        $service = $this->mock(RecommendationService::class);
        $service->shouldReceive('recommendFor')->never();
        $service->shouldReceive('flush')->never();

        Livewire::test(Recommendations::class)
            ->assertSet('items', [])
            ->assertSet('isHydrated', true);
    }
}
