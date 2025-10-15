<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_sees_locked_browse_page(): void
    {
        $this->get(route('browse', ['locale' => 'en']))
            ->assertOk()
            ->assertSee('Premium membership required');
    }

    public function test_non_subscriber_is_redirected_to_checkout(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('account.watch-history', ['locale' => 'en']))
            ->assertRedirect(route('checkout', ['locale' => 'en']))
            ->assertSessionHas('error');
    }

    public function test_checkout_redirects_subscribers_back_to_browse(): void
    {
        $user = User::factory()->create(['stripe_id' => 'cus_test']);
        $user->subscriptions()->create([
            'type' => 'default',
            'stripe_id' => 'sub_test',
            'stripe_status' => 'active',
            'stripe_price' => 'price_monthly',
            'quantity' => 1,
        ]);

        $this->actingAs($user)
            ->get(route('checkout', ['locale' => 'en']))
            ->assertRedirect(route('browse', ['locale' => 'en']))
            ->assertSessionHas('status');
    }

    public function test_subscribers_can_access_watch_history(): void
    {
        $user = User::factory()->create(['stripe_id' => 'cus_live']);
        $user->subscriptions()->create([
            'type' => 'default',
            'stripe_id' => 'sub_live',
            'stripe_status' => 'active',
            'stripe_price' => 'price_monthly',
            'quantity' => 1,
        ]);

        $this->actingAs($user)
            ->get(route('account.watch-history', ['locale' => 'en']))
            ->assertOk();
    }
}
