<?php

namespace Tests\Feature\Livewire;

use App\Livewire\WatchHistoryBrowser;
use App\Models\Movie;
use App\Models\TvShow;
use App\Models\User;
use App\Models\WatchHistory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Tests\TestCase;

class WatchHistoryBrowserTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_subscriber_is_redirected_to_account_with_error(): void
    {
        $user = User::factory()->create();

        $locale = config('app.fallback_locale', 'en');

        $response = $this->actingAs($user)->get(route('account.watch-history', ['locale' => $locale]));

        $response
            ->assertRedirect(route('account', ['locale' => $locale]))
            ->assertSessionHas('error', 'A current subscription is required to browse your watch history.');
    }

    public function test_subscriber_can_filter_and_search_watch_history(): void
    {
        $user = User::factory()->create([
            'stripe_id' => 'cus_test_123',
        ]);

        $user->subscriptions()->create([
            'type' => 'default',
            'stripe_id' => 'sub_test_123',
            'stripe_status' => 'active',
            'stripe_price' => 'price_basic',
            'quantity' => 1,
        ]);

        $movie = Movie::factory()->create([
            'title' => ['en' => 'The Testing Movie'],
        ]);

        $show = TvShow::factory()->create([
            'name' => 'Laravel Adventures',
        ]);

        WatchHistory::factory()->create([
            'user_id' => $user->id,
            'watchable_type' => Movie::class,
            'watchable_id' => $movie->id,
            'watched_at' => Carbon::now()->subDays(3),
        ]);

        WatchHistory::factory()->create([
            'user_id' => $user->id,
            'watchable_type' => TvShow::class,
            'watchable_id' => $show->id,
            'watched_at' => Carbon::now()->subDay(),
        ]);

        Livewire::actingAs($user)
            ->test(WatchHistoryBrowser::class)
            ->assertSee('Browse history')
            ->assertSee($movie->localizedTitle())
            ->assertSee($show->name)
            ->set('type', 'movie')
            ->assertSee($movie->localizedTitle())
            ->assertDontSee($show->name)
            ->set('type', 'tv')
            ->assertSee($show->name)
            ->assertDontSee($movie->localizedTitle())
            ->set('type', 'all')
            ->set('search', 'Testing Movie')
            ->assertSee($movie->localizedTitle())
            ->assertDontSee($show->name);
    }
}
