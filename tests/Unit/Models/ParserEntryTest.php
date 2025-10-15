<?php

namespace Tests\Unit\Models;

use App\Enums\ParserEntryStatus;
use App\Models\ParserEntry;
use App\Models\ParserEntryHistory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParserEntryTest extends TestCase
{
    use RefreshDatabase;

    public function test_casts_status_and_payload_fields(): void
    {
        $entry = ParserEntry::factory()->create([
            'status' => ParserEntryStatus::Approved,
            'payload' => ['title' => ['en' => 'Updated Title']],
            'baseline_snapshot' => ['title' => ['en' => 'Baseline Title']],
        ]);

        $entry->refresh();

        $this->assertSame('Updated Title', $entry->payload['title']['en']);
        $this->assertSame('Baseline Title', $entry->baseline_snapshot['title']['en']);
        $this->assertSame(ParserEntryStatus::Approved, $entry->status);
    }

    public function test_relationships_to_subject_reviewer_and_histories(): void
    {
        $user = User::factory()->create();
        $entry = ParserEntry::factory()->create([
            'reviewed_by' => $user->id,
        ]);

        ParserEntryHistory::factory()->count(2)->create([
            'parser_entry_id' => $entry->id,
            'user_id' => $user->id,
        ]);

        $entry->load('subject', 'reviewer', 'histories');

        $this->assertNotNull($entry->subject);
        $this->assertTrue($entry->reviewer->is($user));
        $this->assertCount(2, $entry->histories);
    }
}
