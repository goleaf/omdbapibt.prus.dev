<?php

namespace Tests\Feature\Livewire\Header;

use App\Livewire\Header\SearchBar;
use App\Models\Movie;
use App\Models\Person;
use App\Models\TvShow;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SearchBarTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_renders_successfully(): void
    {
        Livewire::test(SearchBar::class)
            ->assertStatus(200)
            ->assertViewHas('query')
            ->assertViewHas('results')
            ->assertViewHas('showResults')
            ->assertViewHas('isLoading');
    }

    public function test_it_searches_movies_shows_and_people(): void
    {
        $movie = Movie::factory()->create([
            'title' => ['en' => 'Galactic Quest'],
            'slug' => 'galactic-quest',
            'popularity' => 99.5,
        ]);

        $show = TvShow::factory()->create([
            'name' => 'Galactic Crew',
            'name_translations' => [
                'en' => 'Galactic Crew',
                'es' => 'Tripulación Galáctica',
                'fr' => 'Équipage Galactique',
            ],
            'slug' => 'galactic-crew',
            'popularity' => 88.8,
        ]);

        $person = Person::factory()->create([
            'name' => 'Galactic Sam',
            'slug' => 'galactic-sam',
            'popularity' => 77.7,
        ]);

        Livewire::test(SearchBar::class)
            ->set('query', 'Galactic')
            ->assertSet('showResults', true)
            ->assertSet('results.movies.0.title', $movie->localizedTitle())
            ->assertSet('results.shows.0.title', $show->localizedName())
            ->assertSet('results.people.0.name', $person->name);
    }

    public function test_it_does_not_search_with_short_query(): void
    {
        Movie::factory()->create([
            'title' => ['en' => 'A'],
            'slug' => 'a-movie',
        ]);

        Livewire::test(SearchBar::class)
            ->set('query', 'A')
            ->assertSet('showResults', false)
            ->assertSet('results', []);
    }

    public function test_it_clears_query_and_results(): void
    {
        Movie::factory()->create([
            'title' => ['en' => 'Nova Prime'],
            'slug' => 'nova-prime',
        ]);

        Livewire::test(SearchBar::class)
            ->set('query', 'Nova')
            ->assertSet('showResults', true)
            ->call('clear')
            ->assertSet('query', '')
            ->assertSet('showResults', false)
            ->assertSet('results', [])
            ->assertSet('flatResults', []);
    }

    public function test_it_navigates_results_with_keyboard(): void
    {
        Movie::factory()->createMany([
            [
                'title' => ['en' => 'Searchlight Alpha'],
                'slug' => 'searchlight-alpha',
                'popularity' => 90,
            ],
            [
                'title' => ['en' => 'Searchlight Beta'],
                'slug' => 'searchlight-beta',
                'popularity' => 80,
            ],
        ]);

        $component = Livewire::test(SearchBar::class)
            ->set('query', 'Searchlight')
            ->assertSet('activeIndex', -1);

        $component->call('highlightNext')
            ->assertSet('activeIndex', 0)
            ->call('highlightNext')
            ->assertSet('activeIndex', 1)
            ->call('highlightNext')
            ->assertSet('activeIndex', 0);

        $component->call('highlightPrevious')
            ->assertSet('activeIndex', 1)
            ->call('highlightPrevious')
            ->assertSet('activeIndex', 0)
            ->call('highlightPrevious')
            ->assertSet('activeIndex', 1);
    }

    public function test_it_limits_results_per_category(): void
    {
        Movie::factory()->count(10)->create([
            'title' => ['en' => 'Popular Movie'],
        ]);

        TvShow::factory()->count(10)->create([
            'name' => 'Popular Show',
            'name_translations' => ['en' => 'Popular Show'],
        ]);

        Person::factory()->count(10)->create([
            'name' => 'Popular Person',
        ]);

        $component = Livewire::test(SearchBar::class)
            ->set('query', 'Popular');

        $this->assertCount(5, $component->get('results')['movies']);
        $this->assertCount(5, $component->get('results')['shows']);
        $this->assertCount(5, $component->get('results')['people']);
    }

    public function test_it_responds_to_focus_search_event(): void
    {
        Livewire::test(SearchBar::class)
            ->dispatch('focusSearch')
            ->assertDispatched('focus-search-input');
    }

    public function test_it_responds_to_close_dropdowns_event(): void
    {
        Movie::factory()->create([
            'title' => ['en' => 'Test Movie'],
            'slug' => 'test-movie',
        ]);

        Livewire::test(SearchBar::class)
            ->set('query', 'Test')
            ->assertSet('showResults', true)
            ->dispatch('closeAllDropdowns')
            ->assertSet('showResults', false)
            ->assertSet('activeIndex', -1);
    }

    public function test_it_uses_localized_content(): void
    {
        $movie = Movie::factory()->create([
            'title' => [
                'en' => 'Test Movie',
                'es' => 'Película de Prueba',
                'fr' => 'Film de Test',
            ],
            'slug' => 'test-movie',
            'popularity' => 90,
        ]);

        app()->setLocale('es');

        Livewire::test(SearchBar::class)
            ->set('query', 'Movie')
            ->assertSet('results.movies.0.title', 'Película de Prueba');

        app()->setLocale('fr');

        Livewire::test(SearchBar::class)
            ->set('query', 'Movie')
            ->assertSet('results.movies.0.title', 'Film de Test');
    }

    public function test_it_orders_results_by_popularity(): void
    {
        Movie::factory()->create([
            'title' => ['en' => 'Least Popular'],
            'slug' => 'least-popular',
            'popularity' => 10,
        ]);

        Movie::factory()->create([
            'title' => ['en' => 'Most Popular'],
            'slug' => 'most-popular',
            'popularity' => 100,
        ]);

        Movie::factory()->create([
            'title' => ['en' => 'Medium Popular'],
            'slug' => 'medium-popular',
            'popularity' => 50,
        ]);

        $component = Livewire::test(SearchBar::class)
            ->set('query', 'Popular');

        $results = $component->get('results')['movies'];
        $this->assertEquals('Most Popular', $results[0]['title']);
        $this->assertEquals('Medium Popular', $results[1]['title']);
        $this->assertEquals('Least Popular', $results[2]['title']);
    }

    public function test_it_stores_search_history(): void
    {
        Movie::factory()->create([
            'title' => ['en' => 'Matrix'],
            'slug' => 'matrix',
        ]);

        Livewire::test(SearchBar::class)
            ->set('query', 'Matrix');

        $history = session()->get('search_history', []);
        $this->assertContains('Matrix', $history);
    }

    public function test_it_displays_recent_searches_when_focused(): void
    {
        session()->put('search_history', ['Matrix', 'Inception', 'Avatar']);

        Livewire::test(SearchBar::class)
            ->call('showRecentSearches')
            ->assertSet('showRecent', true);
    }

    public function test_it_selects_recent_search_and_performs_search(): void
    {
        Movie::factory()->create([
            'title' => ['en' => 'Matrix'],
            'slug' => 'matrix',
        ]);

        session()->put('search_history', ['Matrix']);

        Livewire::test(SearchBar::class)
            ->call('selectRecentSearch', 'Matrix')
            ->assertSet('query', 'Matrix')
            ->assertSet('showRecent', false)
            ->assertSet('showResults', true);
    }

    public function test_it_clears_recent_searches(): void
    {
        session()->put('search_history', ['Matrix', 'Inception']);

        Livewire::test(SearchBar::class)
            ->call('clearRecentSearches')
            ->assertSet('showRecent', false);

        $this->assertEmpty(session()->get('search_history', []));
    }

    public function test_it_limits_recent_searches_to_five(): void
    {
        for ($i = 1; $i <= 7; $i++) {
            Movie::factory()->create([
                'title' => ['en' => "Movie {$i}"],
                'slug' => "movie-{$i}",
            ]);
        }

        $component = Livewire::test(SearchBar::class);

        for ($i = 1; $i <= 7; $i++) {
            $component->set('query', "Movie {$i}");
        }

        $history = session()->get('search_history', []);
        $this->assertCount(5, $history);
        $this->assertEquals('Movie 7', $history[0]); // Most recent first
    }

    public function test_it_moves_existing_search_to_top_of_history(): void
    {
        session()->put('search_history', ['Avatar', 'Matrix', 'Inception']);

        Movie::factory()->create([
            'title' => ['en' => 'Matrix'],
            'slug' => 'matrix',
        ]);

        Livewire::test(SearchBar::class)
            ->set('query', 'Matrix');

        $history = session()->get('search_history', []);
        $this->assertEquals('Matrix', $history[0]); // Moved to top
        $this->assertCount(3, $history); // Same count, not duplicated
    }

    public function test_it_does_not_add_to_history_if_no_results(): void
    {
        Livewire::test(SearchBar::class)
            ->set('query', 'NonexistentMovie');

        $history = session()->get('search_history', []);
        $this->assertEmpty($history);
    }
}
