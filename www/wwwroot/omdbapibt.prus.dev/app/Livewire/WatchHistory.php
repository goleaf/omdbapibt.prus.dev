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
            $this->resetPage();
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
