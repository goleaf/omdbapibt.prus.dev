<?php

namespace Tests\Feature\Livewire;

use App\Livewire\MovieDetail;
use App\Models\Movie;
use App\Models\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MovieDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_movie_detail_renders_by_slug(): void
    {
        $movie = Movie::factory()->create();
        $person = Person::factory()->create();

        $movie->people()->attach($person->id, [
            'credit_type' => 'cast',
            'character' => 'Captain Nova',
            'credit_order' => 1,
        ]);

        Livewire::test(MovieDetail::class, ['movie' => $movie->slug])
            ->assertOk()
            ->set('activeTab', 'credits')
            ->assertSee($movie->title)
            ->assertSee('Captain Nova');
    }

    public function test_movie_detail_renders_by_id(): void
    {
        $movie = Movie::factory()->create();

        Livewire::test(MovieDetail::class, ['movie' => (string) $movie->id])
            ->assertOk()
            ->assertSee($movie->title);
    }

    public function test_movie_detail_page_is_accessible_by_slug(): void
    {
        $movie = Movie::factory()->create();

        $this->withoutVite();
        $this->get(route('movies.show', [
            'locale' => config('translatable.fallback_locale'),
            'movie' => $movie->slug,
        ]))
            ->assertOk()
            ->assertSeeLivewire(MovieDetail::class)
            ->assertSee($movie->title);
    }

    public function test_movie_detail_page_is_accessible_by_id(): void
    {
        $movie = Movie::factory()->create();

        $this->withoutVite();
        $this->get(route('movies.show', [
            'locale' => config('translatable.fallback_locale'),
            'movie' => $movie->id,
        ]))
            ->assertOk()
            ->assertSeeLivewire(MovieDetail::class)
            ->assertSee($movie->title);
    }
}
