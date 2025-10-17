<?php

namespace Tests\Feature\Console;

use App\Models\User;
use App\Services\Movies\RecommendationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RefreshRecommendationCacheCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_refreshes_recommendations_for_all_users(): void
    {
        $users = User::factory()->count(3)->create();

        $service = $this->mock(RecommendationService::class);
        $service->shouldReceive('cachedLimits')
            ->times($users->count())
            ->andReturn([12, 24]);
        $service->shouldReceive('flush')
            ->times($users->count())
            ->withArgs(fn (User $passedUser) => $users->contains(fn (User $user) => $user->is($passedUser)));
        $service->shouldReceive('recommendFor')
            ->atLeast()
            ->times($users->count())
            ->andReturn(collect());

        $this->artisan('recommendations:refresh')
            ->expectsOutput('Refreshed recommendations for 3 users.')
            ->assertSuccessful();
    }

    public function test_command_can_limit_to_specific_users(): void
    {
        $target = User::factory()->create();
        User::factory()->create();

        $service = $this->mock(RecommendationService::class);
        $service->shouldReceive('cachedLimits')
            ->once()
            ->andReturn([12]);
        $service->shouldReceive('flush')
            ->once()
            ->withArgs(fn (User $passedUser) => $passedUser->is($target));
        $service->shouldReceive('recommendFor')
            ->atLeast()
            ->once()
            ->andReturn(collect());

        $this->artisan('recommendations:refresh --user='.$target->getKey())
            ->expectsOutput('Refreshed recommendations for 1 users.')
            ->assertSuccessful();
    }
}
