<?php

namespace Tests\Unit\Policies;

use App\Enums\ParserWorkload;
use App\Models\User;
use App\Policies\ParserEntryPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParserEntryPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_admins_can_trigger_all_workloads(): void
    {
        $policy = new ParserEntryPolicy;
        $admin = User::factory()->admin()->create();

        foreach (ParserWorkload::cases() as $workload) {
            $this->assertTrue($policy->trigger($admin, $workload));
        }
    }

    public function test_non_admins_cannot_trigger_any_workload(): void
    {
        $policy = new ParserEntryPolicy;
        $user = User::factory()->create();

        foreach (ParserWorkload::cases() as $workload) {
            $this->assertFalse($policy->trigger($user, $workload));
            $this->assertFalse($policy->trigger(null, $workload));
        }
    }
}
