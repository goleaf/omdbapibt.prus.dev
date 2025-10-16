<?php

namespace Tests\Feature\Livewire;

use App\Enums\SubscriptionStatus;
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

    public function test_non_subscriber_is_redirected_to_checkout_with_error(): void
    {
        $user = User::factory()->create();

        foreach (['en', 'es'] as $locale) {
            $response = $this->actingAs($user)->get(route('account.watch-history', ['locale' => $locale]));

            $response
                ->assertRedirect(route('checkout', ['locale' => $locale]))
                ->assertSessionHas('error', 'A premium subscription is required to access this area.');
        }
    }

    public function test_subscriber_can_filter_and_search_watch_history(): void
    {
        $defaultLocale = app()->getLocale();
        app()->setLocale('es');

        try {
            $user = User::factory()->create([
                'stripe_id' => 'cus_test_123',
            ]);

            $user->subscriptions()->create([
                'type' => 'default',
                'stripe_id' => 'sub_test_123',
                'stripe_status' => SubscriptionStatus::Active->value,
                'stripe_price' => 'price_basic',
                'quantity' => 1,
            ]);

            $movie = Movie::factory()->create([
                'slug' => 'testing-movie',
                'title' => [
                    'en' => 'Testing Movie',
                    'es' => 'Película de Prueba',
                ],
            ]);

            $show = TvShow::factory()->create([
                'slug' => 'laravel-adventures',
                'name' => 'Laravel Adventures',
                'name_translations' => [
                    'en' => 'Laravel Adventures',
                    'es' => 'Aventuras de Laravel',
                ],
            ]);

            WatchHistory::factory()
                ->for($user)
                ->forMovie($movie)
                ->create([
                    'watched_at' => Carbon::now()->subDay(),
                ]);

            WatchHistory::factory()
                ->for($user)
                ->forTvShow($show)
                ->create([
                    'watched_at' => Carbon::now()->subDays(2),
                ]);

            Livewire::actingAs($user)
                ->test(WatchHistoryBrowser::class)
                ->assertSee('Browse history')
                ->assertSee($movie->localizedTitle())
                ->assertSee($show->localizedName())
                ->set('type', 'movie')
                ->assertSee($movie->localizedTitle())
                ->assertDontSee($show->localizedName())
                ->set('type', 'tv')
                ->assertSee($show->localizedName())
                ->assertDontSee($movie->localizedTitle())
                ->set('type', 'all')
                ->assertSee($movie->localizedTitle())
                ->assertSee($show->localizedName())
                ->set('search', 'Testing Movie')
                ->assertSee($movie->localizedTitle())
                ->assertDontSee($show->localizedName());
        } finally {
            app()->setLocale($defaultLocale);
        }
    }

    public function test_watch_history_links_use_localized_movie_routes(): void
    {
        $user = User::factory()->create([
            'stripe_id' => 'cus_link_123',
        ]);

        $user->subscriptions()->create([
            'type' => 'default',
            'stripe_id' => 'sub_link_123',
            'stripe_status' => SubscriptionStatus::Active->value,
            'stripe_price' => 'price_basic',
            'quantity' => 1,
        ]);

        $movie = Movie::factory()->create([
            'slug' => 'localized-watch-history-movie',
            'title' => ['en' => 'Localized History Movie'],
        ]);

        WatchHistory::factory()->for($user)->forMovie($movie)->create([
            'watched_at' => Carbon::now()->subHour(),
        ]);

        $locale = config('app.fallback_locale', 'en');

        Livewire::actingAs($user)
            ->test(WatchHistoryBrowser::class)
            ->assertSee('href="'.route('movies.show', ['locale' => $locale, 'movie' => $movie->slug]).'"', false);
    }
}
