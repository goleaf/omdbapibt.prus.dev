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
        $locale = $this->locale();

        $response = $this->get(route('checkout', ['locale' => $locale]));

        $response->assertRedirect(route('login', ['locale' => $locale]));
    }

    public function test_subscriber_is_redirected_with_flash_message(): void
    {
        $locale = $this->locale();
        $user = User::factory()->create(['stripe_id' => 'cus_test']);

        $user->subscriptions()->create([
            'type' => 'default',
            'stripe_id' => 'sub_test_123',
            'stripe_status' => SubscriptionStatus::Active->value,
            'stripe_price' => 'price_test_123',
            'quantity' => 1,
        ]);

        $response = $this->actingAs($user)->get(route('checkout', ['locale' => $locale]));

        $response->assertRedirect(route('browse', ['locale' => $locale]));
        $response->assertSessionHas('status', __('subscriptions.status.already_subscribed'));
    }

    public function test_configured_plans_are_exposed(): void
    {
        $user = User::factory()->create();

        config()->set('subscriptions.plans', [
            'test-plan' => [
                'name' => 'Test Plan',
                'price_id' => 'price_test',
                'amount' => 1500,
                'currency' => 'usd',
                'interval' => 'month',
                'interval_count' => 1,
                'features' => ['Feature A'],
            ],
            'missing-price' => [
                'name' => 'Missing Price',
                'price_id' => null,
            ],
        ]);

        Livewire::actingAs($user)
            ->test(PlanSelector::class)
            ->assertSet('plans', [
                'test-plan' => [
                    'name' => 'Test Plan',
                    'price_id' => 'price_test',
                    'amount' => 1500,
                    'currency' => 'usd',
                    'interval' => 'month',
                    'interval_count' => 1,
                    'features' => ['Feature A'],
                ],
            ]);
    }

    public function test_start_checkout_dispatches_event(): void
    {
        $user = User::factory()->create();

        config()->set('subscriptions.plans', [
            'premium' => [
                'name' => 'Premium',
                'price_id' => 'price_premium',
                'amount' => 999,
                'currency' => 'usd',
                'interval' => 'month',
                'interval_count' => 1,
                'features' => [],
            ],
        ]);

        Livewire::actingAs($user)
            ->test(PlanSelector::class)
            ->call('startCheckout', 'premium')
            ->assertDispatched('subscriptions.store', priceId: 'price_premium');
    }

    private function locale(): string
    {
        return config('translatable.fallback_locale', config('app.fallback_locale', 'en'));
    }
}
