<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SubscriptionAccessTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function a_user_without_a_subscription_cannot_view_premium_routes(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertForbidden();
    }

    #[Test]
    public function a_user_with_an_active_subscription_can_access_premium_routes(): void
    {
        $user = User::factory()->create();

        $user->subscriptions()->create([
            'name' => 'default',
            'type' => 'default',
            'stripe_id' => 'sub_'.Str::random(24),
            'stripe_status' => 'active',
            'stripe_price' => 'price_monthly',
            'quantity' => 1,
            'trial_ends_at' => now()->addDays(7),
            'ends_at' => null,
        ]);

        $this->actingAs($user->fresh())
            ->get('/dashboard')
            ->assertOk();
    }
}
