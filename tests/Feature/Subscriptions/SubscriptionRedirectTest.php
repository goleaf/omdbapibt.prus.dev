<?php

namespace Tests\Feature\Subscriptions;

use App\Enums\SubscriptionStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_subscribed_user_is_redirected_with_status_message(): void
    {
        $user = User::factory()->create(['stripe_id' => 'cus_subscribed']);

        $user->subscriptions()->create([
            'type' => 'default',
            'stripe_id' => 'sub_active',
            'stripe_status' => SubscriptionStatus::Active->value,
            'stripe_price' => 'price_monthly',
            'quantity' => 1,
        ]);

        $response = $this->actingAs($user)->post(
            route('subscriptions.store', ['locale' => 'en']),
            ['price' => 'price_monthly']
        );

        $response
            ->assertRedirect(route('dashboard', ['locale' => 'en']))
            ->assertSessionHas('status', __('subscriptions.status.already_subscribed'));
    }

    public function test_trialing_user_is_redirected_with_status_message(): void
    {
        $user = User::factory()->create(['stripe_id' => 'cus_trialing']);

        $user->subscriptions()->create([
            'type' => 'default',
            'stripe_id' => 'sub_trialing',
            'stripe_status' => SubscriptionStatus::Trialing->value,
            'stripe_price' => 'price_monthly',
            'quantity' => 1,
            'trial_ends_at' => now()->addDays(5),
        ]);

        $response = $this->actingAs($user)->post(
            route('subscriptions.store', ['locale' => 'en']),
            ['price' => 'price_monthly']
        );

        $response
            ->assertRedirect(route('dashboard', ['locale' => 'en']))
            ->assertSessionHas('status', __('subscriptions.status.already_subscribed'));
    }
}
