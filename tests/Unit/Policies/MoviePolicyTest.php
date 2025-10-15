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

    public function test_view_any_allows_users_with_access(): void
    {
        $user = User::factory()->create();
        $user->subscriptions()->create([
            'type' => 'default',
            'stripe_id' => 'sub_policy',
            'stripe_status' => 'active',
            'stripe_price' => 'price_basic',
            'quantity' => 1,
        ]);

        $policy = new MoviePolicy;

        $this->assertTrue($policy->viewAny($user->fresh()));
    }

    public function test_view_any_blocks_users_without_subscription(): void
    {
        $policy = new MoviePolicy;
        $user = User::factory()->create();

        $this->assertFalse($policy->viewAny($user));
    }

    public function test_view_allows_admin_users(): void
    {
        $policy = new MoviePolicy;
        $user = User::factory()->create(['role' => 'admin']);
        $movie = Movie::factory()->create();

        $this->assertTrue($policy->view($user, $movie));
    }

    public function test_view_denies_ineligible_users(): void
    {
        $policy = new MoviePolicy;
        $user = User::factory()->create();
        $movie = Movie::factory()->create();

        $this->assertFalse($policy->view($user, $movie));
    }
}
