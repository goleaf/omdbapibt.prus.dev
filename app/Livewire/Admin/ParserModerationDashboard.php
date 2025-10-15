<?php

namespace App\Livewire\Admin;

use App\Enums\AdminAuditAction;
use App\Enums\ParserEntryStatus;
use App\Enums\ParserReviewAction;
use App\Models\AdminAuditLog;
use App\Models\ParserEntry;
use App\Models\ParserEntryHistory;
use App\Support\ParserEntryDiffer;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Livewire\Attributes\On;
use Livewire\Component;

class ParserModerationDashboard extends Component
{
    public ?int $selectedEntryId = null;

    public string $decisionNotes = '';

    public function mount(): void
    {
        if (! Auth::check() || ! Auth::user()->isAdmin()) {
            abort(403);
        }
    }

    #[On('refresh-parser-entries')]
    public function refreshEntries(): void
    {
        $this->dispatch('$refresh');
    }

    public function selectEntry(int $entryId): void
    {
        $this->selectedEntryId = $entryId;
        $this->decisionNotes = '';
    }

    public function approve(): void
    {
        $entry = $this->currentEntry();

        if (! $entry) {
            return;
        }

        $diff = $this->buildDiff($entry);

        $entry->fill([
            'status' => ParserEntryStatus::Approved,
            'notes' => $this->decisionNotes ?: null,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => Date::now(),
        ])->save();

        $this->recordHistory($entry, ParserReviewAction::Approved, $diff, $this->decisionNotes);
        $this->logAudit($entry, ParserReviewAction::Approved);

        $this->decisionNotes = '';
        $this->dispatch('parser-entry-reviewed', entryId: $entry->id, decision: ParserReviewAction::Approved->value);
    }

    public function reject(): void
    {
        $entry = $this->currentEntry();

        if (! $entry) {
            return;
        }

        $this->validate([
            'decisionNotes' => ['required', 'string', 'max:2000'],
        ]);

        $diff = $this->buildDiff($entry);

        $entry->fill([
            'status' => ParserEntryStatus::Rejected,
            'notes' => $this->decisionNotes,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => Date::now(),
        ])->save();

        $this->recordHistory($entry, ParserReviewAction::Rejected, $diff, $this->decisionNotes);
        $this->logAudit($entry, ParserReviewAction::Rejected);

        $this->decisionNotes = '';
        $this->dispatch('parser-entry-reviewed', entryId: $entry->id, decision: ParserReviewAction::Rejected->value);
    }

    public function render(): View
    {
        $entries = ParserEntry::query()
            ->latest()
            ->with('subject')
            ->get();

        $selectedEntry = $entries->firstWhere('id', $this->selectedEntryId);

        if (! $selectedEntry && $entries->isNotEmpty()) {
            $selectedEntry = $entries->first();
        }

        if ($selectedEntry) {
            $selectedEntry->load([
                'histories' => fn ($query) => $query->latest()->with('user'),
                'reviewer',
            ]);
            $this->selectedEntryId = $selectedEntry->id;
        }

        $diff = $selectedEntry ? $this->buildDiff($selectedEntry) : [];
        $history = $selectedEntry ? $selectedEntry->histories : collect();

        return view('livewire.admin.parser-moderation-dashboard', [
            'entries' => $entries,
            'selectedEntry' => $selectedEntry,
            'diff' => $diff,
            'history' => $history,
        ]);
    }

    protected function currentEntry(): ?ParserEntry
    {
        if (! $this->selectedEntryId) {
            return null;
        }

        return ParserEntry::query()->find($this->selectedEntryId);
    }

    protected function buildDiff(ParserEntry $entry): array
    {
        $incoming = $entry->payload ?? [];
        $baseline = $entry->baseline_snapshot ?? [];

        if ($baseline === [] && $entry->subject) {
            $baseline = Arr::except($entry->subject->toArray(), ['created_at', 'updated_at']);
        }

        $differ = app(ParserEntryDiffer::class);

        return $differ->diff($baseline, $incoming);
    }

    protected function recordHistory(ParserEntry $entry, ParserReviewAction $action, array $diff, ?string $notes = null): ParserEntryHistory
    {
        return $entry->histories()->create([
            'user_id' => Auth::id(),
            'action' => $action,
            'changes' => $diff,
            'notes' => $notes ?: null,
        ]);
    }

    protected function logAudit(ParserEntry $entry, ParserReviewAction $decision): void
    {
        AdminAuditLog::create([
            'user_id' => Auth::id(),
            'action' => AdminAuditAction::ParserEntryReviewed,
            'details' => [
                'entry_id' => $entry->id,
                'parser' => $entry->parser,
                'decision' => $decision->value,
                'notes' => $this->decisionNotes ?: null,
            ],
        ]);
    }
}
