<?php

namespace Tests\Feature;

use App\Livewire\MovieDetail;
use App\Models\Movie;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class MovieRouteBindingTest extends TestCase
{
    use RefreshDatabase;

    public function test_movie_detail_route_prefers_slug_when_available(): void
    {
        $movie = Movie::factory()->create([
            'title' => 'Inception',
            'slug' => 'inception',
            'translations' => [
                'title' => ['en' => 'Inception'],
            ],
        ]);

        $url = route('movies.show', $movie, false);

        $this->assertTrue(Str::endsWith($url, '/movies/inception'));

        $this->get($url)->assertOk();

        Livewire::test(MovieDetail::class, ['movie' => $movie])
            ->assertSee('Inception', stripInitialData: false);
    }

    public function test_movie_detail_route_falls_back_to_id_when_slug_missing(): void
    {
        $movie = Movie::factory()->create([
            'title' => 'Slugless Feature',
            'slug' => null,
            'translations' => [
                'title' => ['en' => 'Slugless Feature'],
            ],
        ]);

        $url = route('movies.show', $movie, false);

        $this->assertTrue(Str::endsWith($url, '/movies/'.$movie->getKey()));

        $this->get($url)->assertOk();

        Livewire::test(MovieDetail::class, ['movie' => $movie])
            ->assertSee('Slugless Feature', stripInitialData: false);
    }

    public function test_movie_detail_route_can_resolve_by_explicit_id(): void
    {
        $movie = Movie::factory()->create([
            'title' => 'Numeric Lookup',
            'slug' => 'numeric-lookup',
            'translations' => [
                'title' => ['en' => 'Numeric Lookup'],
            ],
        ]);

        $this->get('/movies/'.$movie->getKey())
            ->assertOk();

        Livewire::test(MovieDetail::class, ['movie' => $movie])
            ->assertSee('Numeric Lookup', stripInitialData: false);
    }
}
