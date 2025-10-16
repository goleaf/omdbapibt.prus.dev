<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Watchlist;
use App\Models\Movie;
use App\Models\TvShow;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class WatchlistTest extends TestCase
{
    use RefreshDatabase;

    public function test_watchlist_component_computes_counts_and_links(): void
    {
        $locale = config('translatable.fallback_locale', config('app.fallback_locale'));
        app()->setLocale($locale);

        $user = User::factory()->create();
        $movie = Movie::factory()->create();
        $show = TvShow::factory()->create();

        $user->watchlistedMovies()->attach($movie->getKey());
        $user->watchlistedTvShows()->attach($show->getKey());

        $component = Livewire::actingAs($user)
            ->test(Watchlist::class);

        $expectedShowTitle = $show->name_translations[$locale] ?? $show->name;

        $component
            ->assertOk()
            ->assertSet('locale', $locale)
            ->assertSet('movieCount', 1)
            ->assertSet('showCount', 1)
            ->assertSet('summaryCount', 2)
            ->assertSet('movieLinks.'.$movie->getKey(), route('movies.show', [
                'locale' => $locale,
                'movie' => $movie->slug,
            ]))
            ->assertSet('showLinks.'.$show->getKey(), route('shows.show', [
                'locale' => $locale,
                'slug' => $show->slug,
            ]))
            ->assertSee($movie->localizedTitle())
            ->assertSee($expectedShowTitle)
            ->assertSee(trans_choice(':count title saved|:count titles saved', 2, ['count' => 2]));
    }

    public function test_watchlist_component_defaults_for_guests(): void
    {
        $locale = config('translatable.fallback_locale', config('app.fallback_locale'));
        app()->setLocale($locale);

        Livewire::test(Watchlist::class)
            ->assertOk()
            ->assertSet('locale', $locale)
            ->assertSet('movieCount', 0)
            ->assertSet('showCount', 0)
            ->assertSet('summaryCount', 0)
            ->assertSet('movieLinks', [])
            ->assertSet('showLinks', [])
            ->assertSee(trans_choice(':count title saved|:count titles saved', 0, ['count' => 0]))
            ->assertSee(__('Sign in to start curating your personal watchlist across all your devices.'));
    }
}
