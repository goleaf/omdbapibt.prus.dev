<?php

namespace Tests\Feature;

use App\Livewire\SearchGlobal;
use App\Models\Movie;
use App\Models\Person;
use App\Models\TvShow;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SearchGlobalTest extends TestCase
{
    use RefreshDatabase;

    public function test_groups_results_by_category(): void
    {
        $movie = Movie::factory()->create([
            'title' => 'Galactic Quest',
            'slug' => 'galactic-quest',
            'popularity' => 99.5,
        ]);
        $show = TvShow::factory()->create([
            'name' => 'Galactic Crew',
            'slug' => 'galactic-crew',
            'popularity' => 88.8,
        ]);
        $person = Person::factory()->create([
            'name' => 'Galactic Sam',
            'slug' => 'galactic-sam',
            'popularity' => 77.7,
        ]);

        Livewire::test(SearchGlobal::class)
            ->set('query', 'Galactic')
            ->assertSet('results.movies.0.title', $movie->title)
            ->assertSet('results.tvShows.0.title', $show->name)
            ->assertSet('results.people.0.title', $person->name)
            ->assertSet('flatResults.0.title', $movie->title)
            ->assertSet('flatResults.1.title', $show->name)
            ->assertSet('flatResults.2.title', $person->name)
            ->assertSet('isOpen', true);
    }

    public function test_clears_query_and_results(): void
    {
        Movie::factory()->create([
            'title' => 'Nova Prime',
            'slug' => 'nova-prime',
        ]);

        Livewire::test(SearchGlobal::class)
            ->set('query', 'Nova')
            ->call('clear')
            ->assertSet('query', '')
            ->assertSet('isOpen', false)
            ->assertSet('results.movies', [])
            ->assertSet('flatResults', []);
    }

    public function test_cycles_active_highlight_indexes(): void
    {
        Movie::factory()->createMany([
            [
                'title' => 'Searchlight Alpha',
                'slug' => 'searchlight-alpha',
                'popularity' => 90,
            ],
            [
                'title' => 'Searchlight Beta',
                'slug' => 'searchlight-beta',
                'popularity' => 80,
            ],
        ]);

        $component = Livewire::test(SearchGlobal::class)
            ->set('query', 'Searchlight');

        $component->call('highlightNext')
            ->assertSet('activeIndex', 0)
            ->call('highlightNext')
            ->assertSet('activeIndex', 1)
            ->call('highlightNext')
            ->assertSet('activeIndex', 0)
            ->call('highlightPrevious')
            ->assertSet('activeIndex', 1)
            ->call('closeResults')
            ->assertSet('activeIndex', -1)
            ->assertSet('isOpen', false);
    }
}
