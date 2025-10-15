<?php

namespace Tests\Unit\Models;

use App\Enums\ParserReviewAction;
use App\Models\ParserEntry;
use App\Models\ParserEntryHistory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParserEntryHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_casts_changes_and_action(): void
    {
        $history = ParserEntryHistory::factory()->create([
            'action' => ParserReviewAction::Rejected,
            'changes' => [['field' => 'title', 'before' => 'Old', 'after' => 'New']],
        ]);

        $this->assertSame(ParserReviewAction::Rejected, $history->action);
        $this->assertSame('New', $history->changes[0]['after']);
    }

    public function test_relationships_to_entry_and_user(): void
    {
        $entry = ParserEntry::factory()->create();
        $user = User::factory()->create();

        $history = ParserEntryHistory::factory()->create([
            'parser_entry_id' => $entry->id,
            'user_id' => $user->id,
        ]);

        $this->assertTrue($history->entry->is($entry));
        $this->assertTrue($history->user->is($user));
    }
}
