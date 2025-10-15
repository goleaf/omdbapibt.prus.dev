<?php

namespace App\Livewire;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Watch History')]
#[Layout('layouts.app')]
class WatchHistory extends Component
{
    use WithPagination;

    public string $search = '';

    public string $status = 'all';

    public string $contentType = 'all';

    public string $period = 'all';

    public int $perPage = 15;

    /**
     * Allowed filter values to prevent invalid query combinations.
     */
    protected array $statusOptions = ['all', 'completed', 'in_progress'];

    protected array $contentTypeOptions = ['all', 'movie', 'tv'];

    protected array $periodOptions = ['all', '7', '30', '90', '365'];

    protected array $perPageOptions = [15, 25, 50];

    public string $pageTitle = 'Watch History';

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => 'all'],
        'contentType' => ['except' => 'all'],
        'period' => ['except' => 'all'],
        'perPage' => ['except' => 15],
    ];

    /**
     * Reset the pagination when a filter is updated.
     */
    public function updated($property, $value): void
    {
        if (in_array($property, ['search', 'status', 'contentType', 'period', 'perPage'], true)) {
            $this->sanitizeFilters($property, $value);
            $this->resetPage();
        }
    }

    /**
     * Ensure the component state remains within the allowed options.
     */
    protected function sanitizeFilters(string $property, mixed $value): void
    {
        if ($property === 'status' && ! in_array($value, $this->statusOptions, true)) {
            $this->status = 'all';

            return;
        }

        if ($property === 'contentType' && ! in_array($value, $this->contentTypeOptions, true)) {
            $this->contentType = 'all';

            return;
        }

        if ($property === 'period' && ! in_array($value, $this->periodOptions, true)) {
            $this->period = 'all';

            return;
        }

        if ($property === 'perPage') {
            $perPage = (int) $value;

            if (! in_array($perPage, $this->perPageOptions, true)) {
                $perPage = 15;
            }

            $this->perPage = $perPage;
        }
    }

    /**
     * Render the component.
     */
    public function render()
    {
        return view('livewire.watch-history', [
            'histories' => $this->history(),
        ]);
    }

    /**
     * Build the paginated watch history for the authenticated user.
     */
    protected function history(): LengthAwarePaginator
    {
        $user = Auth::user();

        if (! $user) {
            abort(403);
        }

        $query = $user->watchHistories()->with('watchable');

        if ($this->search !== '') {
            $query->where('content_title', 'like', '%' . $this->search . '%');
        }

        if ($this->status !== 'all') {
            $query->where('status', $this->status);
        }

        if ($this->contentType !== 'all') {
            $query->where('content_type', $this->contentType);
        }

        if ($this->period !== 'all') {
            $days = (int) $this->period;

            if ($days > 0) {
                $query->where('viewed_at', '>=', Carbon::now()->subDays($days));
            }
        }

        return $query
            ->orderByDesc('viewed_at')
            ->paginate($this->perPage)
            ->withQueryString();
    }
}
