<?php

namespace Tests\Feature;

use App\Enums\SubscriptionStatus;
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
            ->assertSessionHas('error', trans('messages.subscription.access_required', [], 'en'));
    }

    public function test_checkout_redirects_subscribers_back_to_browse(): void
    {
        $user = User::factory()->create(['stripe_id' => 'cus_test']);
        $user->subscriptions()->create([
            'type' => 'default',
            'stripe_id' => 'sub_test',
            'stripe_status' => SubscriptionStatus::Active->value,
            'stripe_price' => 'price_monthly',
            'quantity' => 1,
        ]);

        $this->actingAs($user)
            ->get(route('checkout', ['locale' => 'en']))
            ->assertRedirect(route('browse', ['locale' => 'en']))
            ->assertSessionHas('status', trans('messages.subscription.already_active', [], 'en'));
    }

    public function test_checkout_redirects_subscribers_back_to_browse_in_spanish(): void
    {
        $user = User::factory()->create(['stripe_id' => 'cus_es']);
        $user->subscriptions()->create([
            'type' => 'default',
            'stripe_id' => 'sub_es',
            'stripe_status' => SubscriptionStatus::Active->value,
            'stripe_price' => 'price_monthly',
            'quantity' => 1,
        ]);

        $this->actingAs($user)
            ->get(route('checkout', ['locale' => 'es']))
            ->assertRedirect(route('browse', ['locale' => 'es']))
            ->assertSessionHas('status', trans('messages.subscription.already_active', [], 'es'));
    }

    public function test_subscribers_can_access_watch_history(): void
    {
        $user = User::factory()->create(['stripe_id' => 'cus_live']);
        $user->subscriptions()->create([
            'type' => 'default',
            'stripe_id' => 'sub_live',
            'stripe_status' => SubscriptionStatus::Active->value,
            'stripe_price' => 'price_monthly',
            'quantity' => 1,
        ]);

        $this->actingAs($user)
            ->get(route('account.watch-history', ['locale' => 'en']))
            ->assertOk();
    }
}
