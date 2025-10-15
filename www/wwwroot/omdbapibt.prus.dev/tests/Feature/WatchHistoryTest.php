<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\WatchHistory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Cashier\Subscription;
use Tests\TestCase;

class WatchHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_watch_history_requires_active_subscription(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('watch-history'));

        $response->assertRedirect(route('billing.portal'));
    }

    public function test_watch_history_renders_for_subscribed_user(): void
    {
        $user = User::factory()->create([
            'stripe_id' => 'cus_test_123',
        ]);

        Subscription::create([
            'user_id' => $user->id,
            'type' => 'default',
            'stripe_id' => 'sub_test_123',
            'stripe_status' => 'active',
            'stripe_price' => 'price_test_123',
            'quantity' => 1,
        ]);

        WatchHistory::factory()->count(2)->for($user)->create();

        $response = $this->actingAs($user)->get(route('watch-history'));

        $response->assertOk();
        $response->assertSee('Watch History');
        $response->assertSee(WatchHistory::query()->first()->content_title);
    }
}
