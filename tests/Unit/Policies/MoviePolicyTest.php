<?php

namespace Tests\Unit\Policies;

use App\Models\Movie;
use App\Models\User;
use App\Policies\MoviePolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MoviePolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_movies_are_viewable_by_guests(): void
    {
        $movie = Movie::factory()
            ->state([
                'translation_metadata' => [
                    'access' => ['requires_subscription' => false],
                ],
            ])->create();

        $policy = new MoviePolicy;

        $this->assertTrue($policy->view(null, $movie));
    }

    public function test_subscribers_can_view_premium_movies(): void
    {
        $movie = Movie::factory()
            ->state([
                'translation_metadata' => [
                    'access' => ['requires_subscription' => true],
                ],
            ])->create();

        $user = User::factory()->create(['stripe_id' => 'cus_premium']);
        $user->subscriptions()->create([
            'type' => 'default',
            'stripe_id' => 'sub_premium',
            'stripe_status' => 'active',
            'stripe_price' => 'price_monthly',
            'quantity' => 1,
        ]);

        $policy = new MoviePolicy;

        $this->assertTrue($policy->view($user, $movie));
    }

    public function test_guests_cannot_view_premium_movies(): void
    {
        $movie = Movie::factory()
            ->state([
                'translation_metadata' => [
                    'access' => ['requires_subscription' => true],
                ],
            ])->create();

        $policy = new MoviePolicy;

        $this->assertFalse($policy->view(null, $movie));
    }
}
