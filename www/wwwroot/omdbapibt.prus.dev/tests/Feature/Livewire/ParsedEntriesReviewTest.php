<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Admin\ParsedEntriesReview;
use App\Models\ParsedEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ParsedEntriesReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_pending_entries_are_listed_by_default(): void
    {
        $this->actingAs(User::factory()->create());

        $pending = ParsedEntry::factory()->create([
            'parsed_payload' => ['title' => 'Pending Movie', 'year' => 2024],
        ]);

        ParsedEntry::factory()->approved()->create([
            'parsed_payload' => ['title' => 'Approved Movie'],
        ]);

        Livewire::test(ParsedEntriesReview::class)
            ->assertSee('Pending Movie')
            ->assertDontSee('Approved Movie');
    }

    public function test_status_filter_can_include_other_states(): void
    {
        $this->actingAs(User::factory()->create());

        $approved = ParsedEntry::factory()->approved()->create([
            'parsed_payload' => ['title' => 'Ready Movie'],
        ]);

        Livewire::test(ParsedEntriesReview::class)
            ->set('statusFilter', 'approved')
            ->assertSee('Ready Movie');
    }

    public function test_admin_can_approve_entry(): void
    {
        $user = User::factory()->create();
        $entry = ParsedEntry::factory()->create([
            'parsed_payload' => ['title' => 'Thriller A'],
            'original_payload' => ['title' => 'Thriller'],
        ]);

        $this->actingAs($user);

        Livewire::test(ParsedEntriesReview::class)
            ->call('approveEntry', $entry->id);

        $entry->refresh();

        $this->assertEquals('approved', $entry->status);
        $this->assertTrue($entry->is_published);
        $this->assertNotNull($entry->reviewed_at);
        $this->assertEquals($user->id, $entry->reviewed_by);
        $this->assertDatabaseHas('parsed_entry_histories', [
            'parsed_entry_id' => $entry->id,
            'action' => 'approved',
        ]);
    }

    public function test_admin_can_reject_entry_with_comment(): void
    {
        $user = User::factory()->create();
        $entry = ParsedEntry::factory()->create([
            'parsed_payload' => ['title' => 'Drama B'],
        ]);

        $this->actingAs($user);

        Livewire::test(ParsedEntriesReview::class)
            ->set('comment', 'Needs manual verification')
            ->call('rejectEntry', $entry->id);

        $entry->refresh();

        $this->assertEquals('rejected', $entry->status);
        $this->assertFalse($entry->is_published);
        $this->assertDatabaseHas('parsed_entry_histories', [
            'parsed_entry_id' => $entry->id,
            'action' => 'rejected',
            'notes' => 'Needs manual verification',
        ]);
    }

    public function test_comments_are_saved_to_history(): void
    {
        $this->actingAs(User::factory()->create());
        $entry = ParsedEntry::factory()->create();

        Livewire::test(ParsedEntriesReview::class)
            ->set('comment', 'Looks good but double-check actors list')
            ->call('addComment', $entry->id)
            ->assertSet('comment', '');

        $this->assertDatabaseHas('parsed_entry_histories', [
            'parsed_entry_id' => $entry->id,
            'action' => 'comment',
            'notes' => 'Looks good but double-check actors list',
        ]);
    }
}
