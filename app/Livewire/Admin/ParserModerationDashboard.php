<?php

namespace App\Livewire\Admin;

use App\Enums\AdminAuditAction;
use App\Enums\ParserEntryStatus;
use App\Enums\ParserReviewAction;
use App\Livewire\Admin\Forms\ParserModerationDecisionForm;
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

    public ParserModerationDecisionForm $decisionForm;

    public function mount(): void
    {
        $this->authorizeReviewAccess();
    }

    #[On('refresh-parser-entries')]
    public function refreshEntries(): void
    {
        $this->authorizeReviewAccess();
        $this->dispatch('$refresh');
    }

    public function selectEntry(int $entryId): void
    {
        $this->authorizeReviewAccess();
        $this->selectedEntryId = $entryId;
        $this->decisionForm->reset();
        $this->resetErrorBag('decisionForm.notes');
        $this->resetValidation('decisionForm.notes');
    }

    public function approve(): void
    {
        $this->authorizeReviewAccess();
        $entry = $this->currentEntry();

        if (! $entry) {
            return;
        }

        $this->authorize('review', $entry);

        $diff = $this->buildDiff($entry);

        $notes = $this->decisionForm->notes !== '' ? $this->decisionForm->notes : null;

        $entry->fill([
            'status' => ParserEntryStatus::Approved,
            'notes' => $notes,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => Date::now(),
        ])->save();

        $this->recordHistory($entry, ParserReviewAction::Approved, $diff, $notes);
        $this->logAudit($entry, ParserReviewAction::Approved, $notes);

        $this->decisionForm->reset();
        $this->resetErrorBag('decisionForm.notes');
        $this->resetValidation('decisionForm.notes');
        $this->dispatch('parser-entry-reviewed', entryId: $entry->id, decision: ParserReviewAction::Approved->value);
    }

    public function reject(): void
    {
        $this->authorizeReviewAccess();
        $entry = $this->currentEntry();

        if (! $entry) {
            return;
        }

        $this->authorize('review', $entry);

        $validated = $this->decisionForm->validate();
        $notes = $validated['notes'];

        $diff = $this->buildDiff($entry);

        $entry->fill([
            'status' => ParserEntryStatus::Rejected,
            'notes' => $notes,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => Date::now(),
        ])->save();

        $this->recordHistory($entry, ParserReviewAction::Rejected, $diff, $notes);
        $this->logAudit($entry, ParserReviewAction::Rejected, $notes);

        $this->decisionForm->reset();
        $this->resetErrorBag('decisionForm.notes');
        $this->resetValidation('decisionForm.notes');
        $this->dispatch('parser-entry-reviewed', entryId: $entry->id, decision: ParserReviewAction::Rejected->value);
    }

    public function render(): View
    {
        $this->authorizeReviewAccess();

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

        $entry = ParserEntry::query()->find($this->selectedEntryId);

        if (! $entry) {
            return null;
        }

        $this->authorize('view', $entry);

        return $entry;
    }

    protected function authorizeReviewAccess(): void
    {
        $this->authorize('viewAny', ParserEntry::class);
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

    protected function logAudit(ParserEntry $entry, ParserReviewAction $decision, ?string $notes = null): void
    {
        AdminAuditLog::create([
            'user_id' => Auth::id(),
            'action' => AdminAuditAction::ParserEntryReviewed,
            'details' => [
                'entry_id' => $entry->id,
                'parser' => $entry->parser,
                'decision' => $decision->value,
                'notes' => $notes,
            ],
        ]);
    }
}
