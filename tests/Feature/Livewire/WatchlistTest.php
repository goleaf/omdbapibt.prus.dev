<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Watchlist;
use App\Models\ListItem;
use App\Models\ListModel;
use App\Models\Movie;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class WatchlistTest extends TestCase
{
    use RefreshDatabase;

    public function test_watchlist_component_renders_lists_and_items(): void
    {
        $locale = config('translatable.fallback_locale', config('app.fallback_locale'));
        app()->setLocale($locale);

        $user = User::factory()->create();
        $primaryList = ListModel::factory()->watchLater()->for($user)->create();
        $secondaryList = ListModel::factory()->for($user)->create(['title' => 'Festival Picks']);

        $firstMovie = Movie::factory()->create();
        $secondMovie = Movie::factory()->create();

        ListItem::factory()->create([
            'list_id' => $primaryList->getKey(),
            'movie_id' => $firstMovie->getKey(),
            'position' => 1,
        ]);

        ListItem::factory()->create([
            'list_id' => $secondaryList->getKey(),
            'movie_id' => $secondMovie->getKey(),
            'position' => 1,
        ]);

        Livewire::actingAs($user)
            ->test(Watchlist::class)
            ->assertOk()
            ->assertSet('locale', $locale)
            ->assertSet('summaryCount', 2)
            ->assertSee('Watch Later')
            ->assertSee('Festival Picks')
            ->assertSee($firstMovie->localizedTitle())
            ->assertSee($secondMovie->localizedTitle())
            ->assertSee(trans_choice(':count title saved|:count titles saved', 2, ['count' => 2]));
    }

    public function test_watchlist_component_defaults_for_guests(): void
    {
        $locale = config('translatable.fallback_locale', config('app.fallback_locale'));
        app()->setLocale($locale);

        Livewire::test(Watchlist::class)
            ->assertOk()
            ->assertSet('locale', $locale)
            ->assertSet('summaryCount', 0)
            ->assertSee(trans_choice(':count title saved|:count titles saved', 0, ['count' => 0]))
            ->assertSee(__('Sign in to start curating personal lists across all your devices.'));
    }
}
