<?php

namespace Tests\Feature\Livewire\Admin;

use App\Enums\AdminAuditAction;
use App\Enums\ParserEntryStatus;
use App\Enums\ParserReviewAction;
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

    public function test_policy_blocks_non_reviewers_from_livewire_component(): void
    {
        $user = User::factory()->create(['role' => 'subscriber']);

        Livewire::actingAs($user)
            ->test(ParserModerationDashboard::class)
            ->assertForbidden();
    }

    public function test_admin_can_view_pending_parser_entries(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $movie = Movie::factory()->create([
            'title' => ['en' => 'Baseline Film'],
            'overview' => ['en' => 'Original overview'],
            'popularity' => 10,
        ]);

        $baseline = $movie->only(['title', 'overview', 'popularity']);
        $payload = $baseline;
        $payload['title'] = ['en' => 'Updated Film Title'];
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
            'title' => ['en' => 'Baseline Film'],
            'overview' => ['en' => 'Original overview'],
            'popularity' => 10,
        ]);

        $payload = [
            'title' => ['en' => 'Approved Title'],
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
            ->set('decisionForm.notes', 'Looks accurate')
            ->call('approve')
            ->assertDispatched('parser-entry-reviewed', entryId: $entry->id, decision: ParserReviewAction::Approved->value);

        $entry->refresh();

        $this->assertSame(ParserEntryStatus::Approved, $entry->status);
        $this->assertSame('Looks accurate', $entry->notes);
        $this->assertSame($admin->id, $entry->reviewed_by);
        $this->assertNotNull($entry->reviewed_at);

        $this->assertDatabaseHas('parser_entry_histories', [
            'parser_entry_id' => $entry->id,
            'action' => ParserReviewAction::Approved->value,
            'notes' => 'Looks accurate',
        ]);

        $this->assertDatabaseHas('admin_audit_logs', [
            'action' => AdminAuditAction::ParserEntryReviewed->value,
            'details->entry_id' => $entry->id,
            'details->decision' => ParserReviewAction::Approved->value,
        ]);
    }

    public function test_rejecting_entry_requires_notes_and_records_history(): void
    {
        Date::setTestNow('2025-10-16 13:00:00');

        $admin = User::factory()->create(['role' => 'admin']);
        $movie = Movie::factory()->create([
            'title' => ['en' => 'Baseline Film'],
            'overview' => ['en' => 'Original overview'],
            'popularity' => 10,
        ]);

        $payload = [
            'title' => ['en' => 'Rejected Title'],
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
            ->assertHasErrors(['decisionForm.notes' => 'required'])
            ->set('decisionForm.notes', 'Payload missing credits data')
            ->call('reject')
            ->assertDispatched('parser-entry-reviewed', entryId: $entry->id, decision: ParserReviewAction::Rejected->value);

        $entry->refresh();

        $this->assertSame(ParserEntryStatus::Rejected, $entry->status);
        $this->assertSame('Payload missing credits data', $entry->notes);
        $this->assertSame($admin->id, $entry->reviewed_by);
        $this->assertNotNull($entry->reviewed_at);

        $this->assertDatabaseHas('parser_entry_histories', [
            'parser_entry_id' => $entry->id,
            'action' => ParserReviewAction::Rejected->value,
            'notes' => 'Payload missing credits data',
        ]);

        $this->assertDatabaseHas('admin_audit_logs', [
            'action' => AdminAuditAction::ParserEntryReviewed->value,
            'details->entry_id' => $entry->id,
            'details->decision' => ParserReviewAction::Rejected->value,
        ]);
    }

    public function test_rejection_notes_validation_is_localized(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $defaultLocale = app()->getLocale();

        $locales = [
            'en' => 'Rejected because the parsed metadata is incomplete.',
            'es' => 'Rechazado porque los metadatos están incompletos.',
            'fr' => 'Rejeté car les métadonnées sont incomplètes.',
        ];

        foreach ($locales as $locale => $note) {
            $movie = Movie::factory()->create([
                'title' => ['en' => 'Baseline Film'],
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

            app()->setLocale($locale);

            Livewire::actingAs($admin)
                ->test(ParserModerationDashboard::class)
                ->set('selectedEntryId', $entry->id)
                ->call('reject')
                ->assertHasErrors(['decisionForm.notes' => 'required'])
                ->assertSee(trans('parser.moderation.notes_required'));

            $this->assertSame(ParserEntryStatus::Pending, $entry->fresh()->status);

            app()->setLocale($locale);

            Livewire::actingAs($admin)
                ->test(ParserModerationDashboard::class)
                ->set('selectedEntryId', $entry->id)
                ->set('decisionForm.notes', $note)
                ->call('reject')
                ->assertDispatched('parser-entry-reviewed', entryId: $entry->id, decision: ParserReviewAction::Rejected->value);

            $entry->refresh();

            $this->assertSame(ParserEntryStatus::Rejected, $entry->status);
            $this->assertSame($note, $entry->notes);
        }

        app()->setLocale($defaultLocale);
    }

    public function test_authorization_is_rechecked_on_livewire_actions(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $moderator = User::factory()->create();
        $movie = Movie::factory()->create([
            'title' => ['en' => 'Baseline Film'],
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
            ->set('decisionForm.notes', 'Should not work');

        $this->actingAs($moderator);

        $component->call('reject')->assertForbidden();

        $entry->refresh();

        $this->assertSame(ParserEntryStatus::Pending, $entry->status);
        $this->assertNull($entry->reviewed_by);
        $this->assertNull($entry->reviewed_at);
    }
}
