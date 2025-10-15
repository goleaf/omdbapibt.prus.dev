<?php

namespace Tests\Feature\Livewire\Admin;

use App\Livewire\Admin\ParserModerationDashboard;
use App\Models\Movie;
use App\Models\ParserEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Date;
use Livewire\Livewire;
use Tests\TestCase;

class ParserModerationDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_users_cannot_access_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'subscriber']);

        $this->actingAs($user);

        $response = $this->get(route('admin.parser-moderation'));

        $response->assertForbidden();
    }

    public function test_admin_can_view_pending_parser_entries(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $movie = Movie::factory()->create([
            'title' => 'Baseline Film',
            'overview' => ['en' => 'Original overview'],
            'popularity' => 10,
        ]);

        $baseline = $movie->only(['title', 'overview', 'popularity']);
        $payload = $baseline;
        $payload['title'] = 'Updated Film Title';
        $payload['popularity'] = 42.5;

        ParserEntry::factory()
            ->for($movie, 'subject')
            ->create([
                'parser' => 'tmdb',
                'payload' => $payload,
                'baseline_snapshot' => $baseline,
            ]);

        Livewire::actingAs($admin)
            ->test(ParserModerationDashboard::class)
            ->assertSee('Parser Moderation')
            ->assertSee('Updated Film Title')
            ->assertSee('Approve and persist')
            ->assertSee('Reject entry');
    }

    public function test_admin_can_approve_entry_and_logs_history(): void
    {
        Date::setTestNow('2025-10-16 12:00:00');

        $admin = User::factory()->create(['role' => 'admin']);
        $movie = Movie::factory()->create([
            'title' => 'Baseline Film',
            'overview' => ['en' => 'Original overview'],
            'popularity' => 10,
        ]);

        $payload = [
            'title' => 'Approved Title',
            'overview' => ['en' => 'Approved overview'],
            'popularity' => 55.5,
        ];

        $entry = ParserEntry::factory()
            ->for($movie, 'subject')
            ->create([
                'parser' => 'tmdb',
                'payload' => $payload,
                'baseline_snapshot' => $movie->only(['title', 'overview', 'popularity']),
            ]);

        Livewire::actingAs($admin)
            ->test(ParserModerationDashboard::class)
            ->set('selectedEntryId', $entry->id)
            ->set('decisionNotes', 'Looks accurate')
            ->call('approve')
            ->assertDispatched('parser-entry-reviewed', entryId: $entry->id, decision: 'approved');

        $entry->refresh();

        $this->assertSame(ParserEntry::STATUS_APPROVED, $entry->status);
        $this->assertSame('Looks accurate', $entry->notes);
        $this->assertSame($admin->id, $entry->reviewed_by);
        $this->assertNotNull($entry->reviewed_at);

        $this->assertDatabaseHas('parser_entry_histories', [
            'parser_entry_id' => $entry->id,
            'action' => 'approved',
            'notes' => 'Looks accurate',
        ]);

        $this->assertDatabaseHas('admin_audit_logs', [
            'action' => 'parser_entry_reviewed',
            'details->entry_id' => $entry->id,
            'details->decision' => 'approved',
        ]);
    }

    public function test_rejecting_entry_requires_notes_and_records_history(): void
    {
        Date::setTestNow('2025-10-16 13:00:00');

        $admin = User::factory()->create(['role' => 'admin']);
        $movie = Movie::factory()->create([
            'title' => 'Baseline Film',
            'overview' => ['en' => 'Original overview'],
            'popularity' => 10,
        ]);

        $payload = [
            'title' => 'Rejected Title',
            'overview' => ['en' => 'Rejected overview'],
            'popularity' => 5.5,
        ];

        $entry = ParserEntry::factory()
            ->for($movie, 'subject')
            ->create([
                'parser' => 'tmdb',
                'payload' => $payload,
                'baseline_snapshot' => $movie->only(['title', 'overview', 'popularity']),
            ]);

        Livewire::actingAs($admin)
            ->test(ParserModerationDashboard::class)
            ->set('selectedEntryId', $entry->id)
            ->call('reject')
            ->assertHasErrors(['decisionNotes' => 'required'])
            ->set('decisionNotes', 'Payload missing credits data')
            ->call('reject')
            ->assertDispatched('parser-entry-reviewed', entryId: $entry->id, decision: 'rejected');

        $entry->refresh();

        $this->assertSame(ParserEntry::STATUS_REJECTED, $entry->status);
        $this->assertSame('Payload missing credits data', $entry->notes);
        $this->assertSame($admin->id, $entry->reviewed_by);
        $this->assertNotNull($entry->reviewed_at);

        $this->assertDatabaseHas('parser_entry_histories', [
            'parser_entry_id' => $entry->id,
            'action' => 'rejected',
            'notes' => 'Payload missing credits data',
        ]);

        $this->assertDatabaseHas('admin_audit_logs', [
            'action' => 'parser_entry_reviewed',
            'details->entry_id' => $entry->id,
            'details->decision' => 'rejected',
        ]);
    }

    public function test_authorization_is_rechecked_on_livewire_actions(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $moderator = User::factory()->create();
        $movie = Movie::factory()->create([
            'title' => 'Baseline Film',
            'overview' => ['en' => 'Original overview'],
            'popularity' => 10,
        ]);

        $entry = ParserEntry::factory()
            ->for($movie, 'subject')
            ->create([
                'parser' => 'tmdb',
                'payload' => $movie->only(['title', 'overview', 'popularity']),
                'baseline_snapshot' => $movie->only(['title', 'overview', 'popularity']),
            ]);

        $component = Livewire::actingAs($admin)
            ->test(ParserModerationDashboard::class)
            ->set('selectedEntryId', $entry->id);

        $this->actingAs($moderator);

        $component->call('approve')->assertForbidden();

        $component = Livewire::actingAs($admin)
            ->test(ParserModerationDashboard::class)
            ->set('selectedEntryId', $entry->id)
            ->set('decisionNotes', 'Should not work');

        $this->actingAs($moderator);

        $component->call('reject')->assertForbidden();

        $entry->refresh();

        $this->assertSame(ParserEntry::STATUS_PENDING, $entry->status);
        $this->assertNull($entry->reviewed_by);
        $this->assertNull($entry->reviewed_at);
    }
}
