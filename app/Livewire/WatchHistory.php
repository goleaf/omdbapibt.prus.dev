<?php

namespace App\Livewire;

use App\Models\Movie;
use App\Models\TvShow;
use App\Models\User;
use App\Models\WatchHistory as WatchHistoryModel;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class WatchHistory extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'tailwind';

    public string $search = '';

    public string $mediaType = '';

    public string $dateRange = '30';

    public int $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'mediaType' => ['except' => ''],
        'dateRange' => ['except' => '30'],
        'page' => ['except' => 1],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingMediaType(): void
    {
        $this->resetPage();
    }

    public function updatingDateRange(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'mediaType', 'dateRange']);
        $this->resetPage();
    }

    public function render(): View
    {
        return view('livewire.watch-history', [
            'histories' => $this->histories(),
        ])->layout('layouts.dashboard', [
            'title' => 'Watch history',
            'header' => 'Watch history',
            'subheader' => 'Review your recent viewing activity and pick up right where you left off.',
        ]);
    }

    protected function histories(): LengthAwarePaginator
    {
        return $this->buildHistoryQuery()->paginate($this->perPage);
    }

    protected function buildHistoryQuery(): Builder
    {
        $user = Auth::user();

        abort_unless($user instanceof User, 403);

        $query = WatchHistoryModel::query()
            ->with('watchable')
            ->whereBelongsTo($user)
            ->orderByDesc('watched_at')
            ->orderByDesc('id');

        if ($this->mediaType === 'movie') {
            $query->where('watchable_type', Movie::class);
        } elseif ($this->mediaType === 'tv') {
            $query->where('watchable_type', TvShow::class);
        }

        if ($this->search !== '') {
            $search = $this->search;

            $query->whereHasMorph(
                'watchable',
                [Movie::class, TvShow::class],
                function (Builder $builder, string $type) use ($search): void {
                    $builder->where(function (Builder $inner) use ($search, $type): void {
                        if ($type === Movie::class) {
                            $inner->where('title', 'like', '%'.$search.'%')
                                ->orWhere('original_title', 'like', '%'.$search.'%');
                        }

                        if ($type === TvShow::class) {
                            $inner->where('name', 'like', '%'.$search.'%')
                                ->orWhere('original_name', 'like', '%'.$search.'%');
                        }
                    });
                }
            );
        }

        if ($this->dateRange !== '' && $this->dateRange !== 'all') {
            $days = (int) $this->dateRange;

            if ($days > 0) {
                $threshold = now()->subDays($days);

                $query->where(function (Builder $builder) use ($threshold): void {
                    $builder->where('watched_at', '>=', $threshold)
                        ->orWhere(function (Builder $inner) use ($threshold): void {
                            $inner->whereNull('watched_at')
                                ->where('created_at', '>=', $threshold);
                        });
                });
            }
        }

        return $query;
    }
}
