<?php

namespace Tests\Unit\Policies;

use App\Models\Movie;
use App\Models\User;
use App\Policies\MoviePolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MoviePolicyTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function guests_can_view_basic_movie_information(): void
    {
        $movie = Movie::factory()->create();

        $policy = new MoviePolicy();

        $this->assertTrue($policy->view(null, $movie));
    }

    #[Test]
    public function only_subscribers_or_trials_can_view_premium_details(): void
    {
        $policy = new MoviePolicy();
        $movie = Movie::factory()->create();

        $guest = null;
        $this->assertFalse($policy->viewPremium($guest, $movie));

        $user = User::factory()->create();
        $this->assertFalse($policy->viewPremium($user, $movie));

        $user->forceFill(['trial_ends_at' => now()->addDay()])->save();
        $this->assertTrue($policy->viewPremium($user->fresh(), $movie));

        $user->forceFill(['trial_ends_at' => null])->save();

        $user->subscriptions()->create([
            'name' => 'default',
            'type' => 'default',
            'stripe_id' => 'sub_test',
            'stripe_status' => 'active',
            'stripe_price' => 'price_monthly',
            'quantity' => 1,
            'trial_ends_at' => now()->addDays(7),
            'ends_at' => null,
        ]);

        $user->refresh();

        $this->assertTrue($policy->viewPremium($user, $movie));
    }
}
