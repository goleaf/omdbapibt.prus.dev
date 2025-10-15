<?php

namespace Tests\Unit\Subscriptions;

use App\Models\User;
use App\Support\Subscriptions\BillingState;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BillingStateTest extends TestCase
{
    use RefreshDatabase;

    public function test_reports_active_subscription_details(): void
    {
        $user = User::factory()->create(['stripe_id' => 'cus_456']);
        $user->subscriptions()->create([
            'type' => 'default',
            'stripe_id' => 'sub_456',
            'stripe_status' => 'active',
            'stripe_price' => 'price_yearly',
            'quantity' => 1,
            'trial_ends_at' => now()->addDays(5)->startOfDay(),
        ]);

        $state = new BillingState($user);

        $this->assertTrue($state->hasActiveSubscription());
        $this->assertTrue($state->canAccessPortal());
        $this->assertSame(5, $state->trialDaysRemaining());
    }

    public function test_handles_absent_subscriptions(): void
    {
        $user = User::factory()->create();

        $state = new BillingState($user);

        $this->assertFalse($state->hasActiveSubscription());
        $this->assertFalse($state->canAccessPortal());
        $this->assertSame(0, $state->trialDaysRemaining());
    }
}
