<?php

namespace Tests\Unit\Models;

use App\Enums\AdminAuditAction;
use App\Models\AdminAuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuditLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_action_and_details_are_cast(): void
    {
        $user = User::factory()->create();

        $log = AdminAuditLog::create([
            'user_id' => $user->id,
            'action' => AdminAuditAction::ParserEntryReviewed,
            'details' => ['parser_entry_id' => 123],
        ]);

        $this->assertInstanceOf(AdminAuditAction::class, $log->action);
        $this->assertTrue($log->action === AdminAuditAction::ParserEntryReviewed);
        $this->assertSame(['parser_entry_id' => 123], $log->details);
    }

    public function test_user_relationship_returns_owner(): void
    {
        $user = User::factory()->create();

        $log = AdminAuditLog::create([
            'user_id' => $user->id,
            'action' => AdminAuditAction::ParserEntryReviewed,
            'details' => [],
        ]);

        $this->assertTrue($log->user->is($user));
    }
}
