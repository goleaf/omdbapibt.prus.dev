<?php

namespace Tests\Unit\Models;

use App\Enums\UserManagementAction;
use App\Models\User;
use App\Models\UserManagementLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_casts_action_and_details(): void
    {
        $actor = User::factory()->create();
        $subject = User::factory()->create();

        $log = UserManagementLog::create([
            'actor_id' => $actor->id,
            'user_id' => $subject->id,
            'action' => UserManagementAction::ImpersonationStopped,
            'details' => ['reason' => 'User request'],
        ]);

        $log->refresh();

        $this->assertSame(UserManagementAction::ImpersonationStopped, $log->action);
        $this->assertSame('User request', $log->details['reason']);
    }

    public function test_relationships_to_actor_and_user(): void
    {
        $actor = User::factory()->create();
        $subject = User::factory()->create();

        $log = UserManagementLog::create([
            'actor_id' => $actor->id,
            'user_id' => $subject->id,
            'action' => UserManagementAction::QueuedCommand,
            'details' => [],
        ]);

        $this->assertTrue($log->actor->is($actor));
        $this->assertTrue($log->user->is($subject));
    }
}
