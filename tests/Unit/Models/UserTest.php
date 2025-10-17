<?php

namespace Tests\Unit\Models;

use App\Enums\UserManagementAction;
use App\Enums\UserRole;
use App\Models\ListModel;
use App\Models\Movie;
use App\Models\Rating;
use App\Models\User;
use App\Models\UserManagementLog;
use App\Models\WatchHistory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Cashier\Subscription;
use Mockery;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_role_helpers_reflect_assigned_role(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();

        $this->assertTrue($admin->isAdmin());
        $this->assertSame('Administrator', $admin->roleLabel());
        $this->assertTrue($admin->canImpersonate());
        $this->assertFalse($admin->canBeImpersonated());

        $this->assertFalse($user->isAdmin());
        $this->assertSame('User', $user->roleLabel());
        $this->assertFalse($user->canImpersonate());
        $this->assertTrue($user->canBeImpersonated());
    }

    public function test_watch_later_helpers_manage_movies(): void
    {
        $user = User::factory()->create();
        $movie = Movie::factory()->create();

        $list = $user->ensureWatchLaterList();

        $this->assertInstanceOf(ListModel::class, $list);
        $this->assertTrue($list->isWatchLater());

        $list->items()->create([
            'movie_id' => $movie->getKey(),
            'position' => 1,
        ]);

        $this->assertTrue($user->hasInWatchLater($movie));
        $this->assertFalse($user->hasInWatchLater(Movie::factory()->create()));
    }

    public function test_watch_history_and_management_log_relationships(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $history = WatchHistory::factory()->create([
            'user_id' => $user->id,
        ]);

        $managed = UserManagementLog::create([
            'actor_id' => $otherUser->id,
            'user_id' => $user->id,
            'action' => UserManagementAction::RoleUpdated,
            'details' => ['from' => UserRole::User->value, 'to' => UserRole::Subscriber->value],
        ]);

        $acted = UserManagementLog::create([
            'actor_id' => $user->id,
            'user_id' => $otherUser->id,
            'action' => UserManagementAction::ImpersonationStarted,
            'details' => [],
        ]);

        $user->load('watchHistories', 'managementLogs', 'actedManagementLogs');

        $this->assertTrue($user->watchHistories->contains($history));
        $this->assertTrue($user->managementLogs->contains($managed));
        $this->assertTrue($user->actedManagementLogs->contains($acted));
    }

    public function test_has_premium_access_checks_subscription_states(): void
    {
        /** @var User $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('subscribed')->with('default')->andReturnFalse();
        $user->shouldReceive('onTrial')->with('default')->andReturnFalse();
        $user->shouldReceive('subscription')->with('default')->andReturn(null);

        $this->assertFalse($user->hasPremiumAccess());

        $subscription = Mockery::mock(Subscription::class);
        $subscription->shouldReceive('onGracePeriod')->andReturnTrue();

        /** @var User $userWithGrace */
        $userWithGrace = Mockery::mock(User::class)->makePartial();
        $userWithGrace->shouldReceive('subscribed')->with('default')->andReturnFalse();
        $userWithGrace->shouldReceive('onTrial')->with('default')->andReturnFalse();
        $userWithGrace->shouldReceive('subscription')->with('default')->andReturn($subscription);

        $this->assertTrue($userWithGrace->hasPremiumAccess());
    }

    public function test_can_access_billing_portal_requires_valid_subscription(): void
    {
        /** @var User $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('hasStripeId')->andReturnFalse();

        $this->assertFalse($user->canAccessBillingPortal());

        $subscription = Mockery::mock(Subscription::class);
        $subscription->shouldReceive('valid')->andReturnFalse();
        $subscription->shouldReceive('onGracePeriod')->andReturnTrue();
        $subscription->shouldReceive('onTrial')->andReturnFalse();

        /** @var User $eligible */
        $eligible = Mockery::mock(User::class)->makePartial();
        $eligible->shouldReceive('hasStripeId')->andReturnTrue();
        $eligible->shouldReceive('subscription')->with('default')->andReturn($subscription);

        $this->assertTrue($eligible->canAccessBillingPortal());
    }

    public function test_rating_accessors_reflect_user_reactions(): void
    {
        $user = User::factory()->create();
        $movie = Movie::factory()->create();
        $otherMovie = Movie::factory()->create();

        Rating::factory()->create([
            'user_id' => $user->id,
            'movie_id' => $movie->id,
            'rating' => 3,
            'liked' => false,
            'disliked' => true,
        ]);

        $this->assertSame(3, $user->ratingScoreForMovie($movie));
        $this->assertFalse($user->hasLikedMovie($movie));
        $this->assertTrue($user->hasDislikedMovie($movie));

        $this->assertNull($user->ratingScoreForMovie($otherMovie));
        $this->assertFalse($user->hasLikedMovie($otherMovie));
        $this->assertFalse($user->hasDislikedMovie($otherMovie));
    }
}
