<?php

namespace Tests\Feature;

use App\Models\Movie;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BrowserFlowTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function visitors_can_complete_the_signup_flow(): void
    {
        $response = $this->post('/signup', [
            'name' => 'Browser Tester',
            'email' => 'browser@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');

        $this->assertDatabaseHas('users', [
            'email' => 'browser@example.com',
        ]);

        $this->assertAuthenticated();
    }

    #[Test]
    public function subscribers_can_checkout_a_plan_without_stripe_calls(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/subscribe', ['plan' => 'premium_monthly'])
            ->assertRedirect(route('browse'));

        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $user->id,
            'name' => 'default',
            'stripe_status' => 'active',
        ]);
    }

    #[Test]
    public function subscribers_can_browse_the_catalog_end_to_end(): void
    {
        $user = User::factory()->create();
        Movie::factory()->count(3)->create();

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
            ->get('/browse')
            ->assertOk()
            ->assertSee('Browse Catalog')
            ->assertSee(Movie::first()->title);
    }
}
