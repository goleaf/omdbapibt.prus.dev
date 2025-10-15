<?php

namespace App\Livewire\Admin;

use App\Models\AdminAuditLog;
use App\Models\ParserEntry;
use App\Models\ParserEntryHistory;
use App\Support\ParserEntryDiffer;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Livewire\Attributes\On;
use Livewire\Component;

class ParserModerationDashboard extends Component
{
    use AuthorizesRequests;

    public ?int $selectedEntryId = null;

    public string $decisionNotes = '';

    public function mount(): void
    {
        $this->authorizeReview();
    }

    #[On('refresh-parser-entries')]
    public function refreshEntries(): void
    {
        $this->authorizeReview();
        $this->dispatch('$refresh');
    }

    public function selectEntry(int $entryId): void
    {
        $this->authorizeReview();
        $this->selectedEntryId = $entryId;
        $this->decisionNotes = '';
    }

    public function approve(): void
    {
        $this->authorizeReview();
        $entry = $this->currentEntry();

        if (! $entry) {
            return;
        }

        $this->authorize('update', $entry);

        $diff = $this->buildDiff($entry);

        $entry->fill([
            'status' => ParserEntry::STATUS_APPROVED,
            'notes' => $this->decisionNotes ?: null,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => Date::now(),
        ])->save();

        $this->recordHistory($entry, 'approved', $diff, $this->decisionNotes);
        $this->logAudit($entry, 'approved');

        $this->decisionNotes = '';
        $this->dispatch('parser-entry-reviewed', entryId: $entry->id, decision: 'approved');
    }

    public function reject(): void
    {
        $this->authorizeReview();
        $entry = $this->currentEntry();

        if (! $entry) {
            return;
        }

        $this->authorize('update', $entry);

        $this->validate([
            'decisionNotes' => ['required', 'string', 'max:2000'],
        ]);

        $diff = $this->buildDiff($entry);

        $entry->fill([
            'status' => ParserEntry::STATUS_REJECTED,
            'notes' => $this->decisionNotes,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => Date::now(),
        ])->save();

        $this->recordHistory($entry, 'rejected', $diff, $this->decisionNotes);
        $this->logAudit($entry, 'rejected');

        $this->decisionNotes = '';
        $this->dispatch('parser-entry-reviewed', entryId: $entry->id, decision: 'rejected');
    }

    public function render(): View
    {
        $this->authorizeReview();

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

    protected function authorizeReview(): void
    {
        $this->authorize('review', ParserEntry::class);
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

    protected function recordHistory(ParserEntry $entry, string $action, array $diff, ?string $notes = null): ParserEntryHistory
    {
        return $entry->histories()->create([
            'user_id' => Auth::id(),
            'action' => $action,
            'changes' => $diff,
            'notes' => $notes ?: null,
        ]);
    }

    protected function logAudit(ParserEntry $entry, string $decision): void
    {
        AdminAuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'parser_entry_reviewed',
            'details' => [
                'entry_id' => $entry->id,
                'parser' => $entry->parser,
                'decision' => $decision,
                'notes' => $this->decisionNotes ?: null,
            ],
        ]);
    }
}
