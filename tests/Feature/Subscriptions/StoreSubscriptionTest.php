<?php

namespace Tests\Feature\Subscriptions;

use App\Enums\SubscriptionStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Laravel\Cashier\SubscriptionBuilder;
use Mockery;
use Tests\TestCase;

class StoreSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_subscribed_user_is_redirected_with_status_message(): void
    {
        foreach ($this->supportedLocales() as $locale) {
            $user = User::factory()->create(['stripe_id' => "cus_existing_{$locale}"]);
            $user->subscriptions()->create([
                'type' => 'default',
                'stripe_id' => "sub_existing_{$locale}",
                'stripe_status' => SubscriptionStatus::Active->value,
                'stripe_price' => 'price_monthly',
                'quantity' => 1,
            ]);

            $response = $this->actingAs($user)
                ->post(route('subscriptions.store', ['locale' => $locale]), [
                    'price' => 'price_monthly',
                ]);

            $response->assertRedirect(route('dashboard', ['locale' => $locale]))
                ->assertSessionHas('status', __('subscriptions.status.already_subscribed', locale: $locale));
        }
    }

    /**
     * @return array<int, string>
     */
    protected function supportedLocales(): array
    {
        return config('translatable.locales', ['en']);
    }

    public function test_trial_user_is_redirected_to_checkout_session(): void
    {
        Config::set('services.stripe.trial_days', 7);

        $builder = Mockery::mock(SubscriptionBuilder::class);

        $builder->shouldReceive('trialDays')
            ->once()
            ->with(7)
            ->andReturnSelf();

        $builder->shouldReceive('checkout')
            ->once()
            ->with(Mockery::on(function (array $payload): bool {
                return isset($payload['success_url'], $payload['cancel_url'], $payload['metadata'])
                    && $payload['metadata']['type'] === 'premium'
                    && $payload['metadata']['name'] === 'default'
                    && $payload['metadata']['user_id'] === 1;
            }))
            ->andReturn(redirect('/stripe/checkout/session'));

        $user = Mockery::mock(User::class)->makePartial();
        $user->forceFill([
            'id' => 1,
            'name' => 'Trial User',
            'email' => 'trial@example.com',
        ]);
        $user->exists = true;
        $user->shouldReceive('getAuthIdentifier')->andReturn(1);
        $user->shouldReceive('getAuthIdentifierName')->andReturn('id');
        $user->shouldReceive('getKey')->andReturn(1);
        $user->shouldReceive('save')->andReturnTrue();
        $user->shouldReceive('subscribed')->with('default')->andReturnFalse();
        $user->shouldReceive('newSubscription')
            ->once()
            ->with('default', 'price_trial')
            ->andReturn($builder);

        $this->actingAs($user);

        $response = $this->post(route('subscriptions.store', ['locale' => 'en']), [
            'price' => 'price_trial',
        ]);

        $response->assertRedirect('/stripe/checkout/session');
    }
}
