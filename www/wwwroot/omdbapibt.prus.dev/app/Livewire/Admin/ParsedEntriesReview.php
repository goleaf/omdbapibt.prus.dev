<?php

namespace App\Livewire\Admin;

use App\Models\ParsedEntry;
use Illuminate\Contracts\Pagination\Paginator;
use Livewire\Component;
use Livewire\WithPagination;

class ParsedEntriesReview extends Component
{
    use WithPagination;

    public string $statusFilter = 'pending';
    public ?int $selectedEntryId = null;
    public string $comment = '';

    protected $paginationTheme = 'tailwind';

    protected $queryString = [
        'statusFilter' => ['except' => 'pending'],
    ];

    protected function rules(): array
    {
        return [
            'comment' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function selectEntry(int $entryId): void
    {
        $this->selectedEntryId = $entryId;
        $this->comment = '';
        $this->resetErrorBag();
    }

    public function approveEntry(int $entryId): void
    {
        $this->handleDecision($entryId, 'approved', true);
    }

    public function rejectEntry(int $entryId): void
    {
        $this->handleDecision($entryId, 'rejected', false);
    }

    protected function handleDecision(int $entryId, string $status, bool $published): void
    {
        $entry = ParsedEntry::query()->findOrFail($entryId);

        $entry->forceFill([
            'status' => $status,
            'is_published' => $published,
            'reviewed_at' => now(),
            'reviewed_by' => optional(auth())->id(),
        ])->save();

        $this->recordHistory($entry, $status, $this->comment ?: null);

        $this->comment = '';
        $this->selectedEntryId = $published ? null : $this->selectedEntryId;

        $this->dispatch('entry-reviewed');
    }

    public function addComment(int $entryId): void
    {
        $this->validateOnly('comment');

        if (blank($this->comment)) {
            return;
        }

        $entry = ParsedEntry::query()->findOrFail($entryId);
        $this->recordHistory($entry, 'comment', $this->comment);
        $this->comment = '';
    }

    protected function recordHistory(ParsedEntry $entry, string $action, ?string $notes = null): void
    {
        $entry->histories()->create([
            'user_id' => optional(auth())->id(),
            'action' => $action,
            'notes' => $notes,
        ]);
    }

    public function getSelectedEntryProperty(): ?ParsedEntry
    {
        if (! $this->selectedEntryId) {
            return null;
        }

        return ParsedEntry::query()
            ->with([
                'parseable',
                'histories' => fn ($query) => $query->latest('created_at')->with('user'),
            ])
            ->find($this->selectedEntryId);
    }

    public function render()
    {
        return view('livewire.admin.parsed-entries-review', [
            'entries' => $this->entries(),
        ]);
    }

    protected function entries(): Paginator
    {
        return ParsedEntry::query()
            ->with('parseable')
            ->when($this->statusFilter !== 'all', function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->latest()
            ->paginate(10);
    }

    public function statusOptions(): array
    {
        return [
            'pending' => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'all' => 'All',
        ];
    }

    public function summaryForEntry(ParsedEntry $entry): string
    {
        $title = data_get($entry->parsed_payload, 'title') ?? data_get($entry->parsed_payload, 'name');
        $year = data_get($entry->parsed_payload, 'year');

        $parts = array_filter([$title, $year ? "({$year})" : null]);

        return $parts ? implode(' ', $parts) : "Entry #{$entry->id}";
    }
}
