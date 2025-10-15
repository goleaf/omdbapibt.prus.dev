<?php

namespace Tests\Feature\Livewire;

use App\Livewire\WatchHistory as WatchHistoryComponent;
use App\Models\Movie;
use App\Models\TvShow;
use App\Models\User;
use App\Models\WatchHistory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class WatchHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_watch_history_route_requires_active_subscription(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('watch-history'))
            ->assertForbidden();
    }

    public function test_subscribed_users_can_filter_watch_history(): void
    {
        $user = User::factory()->create();

        $user->subscriptions()->create([
            'name' => 'default',
            'stripe_id' => 'sub_test_123',
            'stripe_status' => 'active',
            'stripe_price' => 'price_basic',
            'quantity' => 1,
            'trial_ends_at' => null,
        ]);

        $recentMovie = Movie::factory()->create([
            'title' => 'Galaxy Quest',
        ]);

        $recentShow = TvShow::factory()->create([
            'name' => 'Space Oddity',
        ]);

        $olderShow = TvShow::factory()->create([
            'name' => 'Retro Future',
        ]);

        WatchHistory::factory()
            ->for($user)
            ->forMovie($recentMovie)
            ->create([
                'progress_percent' => 100,
                'completed' => true,
                'watched_at' => now()->subDays(2),
            ]);

        WatchHistory::factory()
            ->for($user)
            ->forTvShow($recentShow)
            ->create([
                'progress_percent' => 45,
                'completed' => false,
                'watched_at' => now()->subDays(3),
            ]);

        WatchHistory::factory()
            ->for($user)
            ->forTvShow($olderShow)
            ->create([
                'progress_percent' => 80,
                'completed' => true,
                'watched_at' => now()->subDays(60),
            ]);

        $component = Livewire::actingAs($user)
            ->test(WatchHistoryComponent::class)
            ->assertSee('Galaxy Quest')
            ->assertSee('Space Oddity')
            ->assertDontSee('Retro Future');

        $component->set('mediaType', 'tv')
            ->assertSee('Space Oddity')
            ->assertDontSee('Galaxy Quest');

        $component->set('search', 'Galaxy')
            ->assertDontSee('Space Oddity')
            ->assertDontSee('Galaxy Quest');

        $component->call('resetFilters')
            ->assertSet('mediaType', '')
            ->assertSet('dateRange', '30');

        $component->set('dateRange', '7')
            ->assertSee('Galaxy Quest')
            ->assertSee('Space Oddity');
    }
}
