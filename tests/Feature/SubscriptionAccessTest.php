<?php

namespace Tests\Feature;

use App\Models\Movie;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class SubscriptionAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        if (! Route::has('logout')) {
            Route::any('/logout', fn () => '')->name('logout');
        }
    }

    public function test_guest_is_redirected_to_signup_when_browsing(): void
    {
        $response = $this->get('/browse');

        $response->assertRedirect(route('signup'));
    }

    public function test_non_subscriber_is_redirected_to_pricing(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/browse');

        $response->assertRedirect(route('pricing'));
        $response->assertSessionHas('status', __('A subscription is required to access this section.'));
    }

    public function test_subscribed_user_can_access_browse(): void
    {
        $user = User::factory()->create();

        $user->subscriptions()->create([
            'type' => 'default',
            'stripe_id' => 'sub_123',
            'stripe_status' => 'active',
            'stripe_price' => 'price_basic',
            'quantity' => 1,
        ]);

        $response = $this->actingAs($user)->get('/browse');

        $response->assertOk();
    }

    public function test_subscribed_user_can_view_movie_detail(): void
    {
        $user = User::factory()->create();

        $user->subscriptions()->create([
            'type' => 'default',
            'stripe_id' => 'sub_456',
            'stripe_status' => 'active',
            'stripe_price' => 'price_premium',
            'quantity' => 1,
        ]);

        $movie = Movie::factory()->create();

        $response = $this->actingAs($user)->get(route('movies.show', $movie));

        $response->assertOk();
        $response->assertViewIs('pages.movies.show');
        $response->assertViewHas('slug', $movie->slug);
    }

    public function test_guest_checkout_redirects_to_signup(): void
    {
        $response = $this->get('/subscriptions/checkout');

        $response->assertRedirect(route('signup'));
    }

    public function test_checkout_redirects_subscribed_users_to_browse(): void
    {
        $user = User::factory()->create();

        $user->subscriptions()->create([
            'type' => 'default',
            'stripe_id' => 'sub_789',
            'stripe_status' => 'active',
            'stripe_price' => 'price_basic',
            'quantity' => 1,
        ]);

        $response = $this->actingAs($user)->get('/subscriptions/checkout');

        $response->assertRedirect(route('browse'));
        $response->assertSessionHas('status', __('You already have an active subscription.'));
    }

    public function test_checkout_view_available_for_authenticated_non_subscriber(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/subscriptions/checkout');

        $response->assertOk();
        $response->assertViewIs('pages.subscriptions.checkout');
        $response->assertViewHasAll(['monthlyPrice', 'yearlyPrice', 'trialDays']);
    }
}
