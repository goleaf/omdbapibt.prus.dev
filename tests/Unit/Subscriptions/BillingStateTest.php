<?php

namespace Tests\Unit\Subscriptions;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BillingStateTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_subscription_grants_access(): void
    {
        $user = User::factory()->create();

        $user->subscriptions()->create([
            'type' => 'default',
            'stripe_id' => 'sub_active',
            'stripe_status' => 'active',
            'stripe_price' => 'price_basic',
            'quantity' => 1,
        ]);

        $this->assertTrue($user->fresh()->hasSubscriptionAccess());
    }

    public function test_incomplete_subscription_does_not_grant_access(): void
    {
        $user = User::factory()->create();

        $user->subscriptions()->create([
            'type' => 'default',
            'stripe_id' => 'sub_incomplete',
            'stripe_status' => 'incomplete',
            'stripe_price' => 'price_basic',
            'quantity' => 1,
        ]);

        $this->assertFalse($user->fresh()->hasSubscriptionAccess());
    }

    public function test_generic_trial_grants_access(): void
    {
        $user = User::factory()->create([
            'trial_ends_at' => now()->addDays(5),
        ]);

        $this->assertTrue($user->fresh()->hasSubscriptionAccess());
    }

    public function test_admin_has_access_without_subscription(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->assertTrue($user->hasSubscriptionAccess());
    }

    public function test_regular_user_without_subscription_has_no_access(): void
    {
        $user = User::factory()->create();

        $this->assertFalse($user->hasSubscriptionAccess());
    }
}
