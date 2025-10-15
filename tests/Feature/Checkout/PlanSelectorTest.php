<?php

namespace Tests\Feature\Checkout;

use App\Enums\SubscriptionStatus;
use App\Livewire\Checkout\PlanSelector;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PlanSelectorTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        Livewire::test(PlanSelector::class)
            ->assertRedirect(route('login', ['locale' => app()->getLocale()]));
    }

    public function test_existing_subscriber_is_redirected_to_browse_with_status(): void
    {
        $user = User::factory()->create([
            'stripe_id' => 'cus_checkout_subscriber',
        ]);

        $user->subscriptions()->create([
            'type' => 'default',
            'stripe_id' => 'sub_checkout_123',
            'stripe_status' => SubscriptionStatus::Active->value,
            'stripe_price' => 'price_checkout_monthly',
            'quantity' => 1,
        ]);

        Livewire::actingAs($user)
            ->test(PlanSelector::class)
            ->assertRedirect(route('browse', ['locale' => app()->getLocale()]))
            ->assertSessionHas('status', __('subscriptions.status.already_subscribed'));
    }

    public function test_available_plans_are_exposed_from_configuration(): void
    {
        config()->set('services.stripe.prices.monthly', 'price_test_monthly');
        config()->set('services.stripe.prices.yearly', 'price_test_yearly');

        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(PlanSelector::class)
            ->assertViewHas('plans', function (array $plans): bool {
                return $plans === [
                    'monthly' => 'price_test_monthly',
                    'yearly' => 'price_test_yearly',
                ];
            });
    }

    public function test_selecting_a_plan_dispatches_subscription_event(): void
    {
        config()->set('services.stripe.prices.monthly', 'price_dispatch_monthly');
        config()->set('services.stripe.prices.yearly', null);

        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(PlanSelector::class)
            ->call('selectPlan', 'price_dispatch_monthly')
            ->assertDispatched('subscriptions.store', price: 'price_dispatch_monthly');
    }
}
