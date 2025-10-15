<?php

namespace Tests\Unit\Billing;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CashierBillingTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_recognises_active_subscriptions_and_trials(): void
    {
        $user = User::factory()->create([
            'trial_ends_at' => now()->addDay(),
        ]);

        $this->assertTrue($user->onTrial());

        $user->subscriptions()->create([
            'name' => 'default',
            'type' => 'default',
            'stripe_id' => 'sub_'.Str::random(24),
            'stripe_status' => 'active',
            'stripe_price' => 'price_yearly',
            'quantity' => 1,
            'trial_ends_at' => now()->addDays(7),
            'ends_at' => null,
        ]);

        $user->load('subscriptions');

        $this->assertTrue($user->subscribed('default'));
        $this->assertTrue($user->subscribedToPrice('price_yearly', 'default'));
    }

    #[Test]
    public function it_reports_when_a_subscription_has_ended(): void
    {
        $user = User::factory()->create();

        $user->subscriptions()->create([
            'name' => 'default',
            'type' => 'default',
            'stripe_id' => 'sub_'.Str::random(24),
            'stripe_status' => 'canceled',
            'stripe_price' => 'price_monthly',
            'quantity' => 1,
            'trial_ends_at' => now()->subDay(),
            'ends_at' => now()->subDay(),
        ]);

        $this->assertFalse($user->fresh()->subscribed('default'));
    }
}
