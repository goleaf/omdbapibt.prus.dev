<?php

namespace App\Livewire;

use App\Models\Movie;
use App\Models\TvShow;
use App\Models\User;
use App\Models\WatchHistory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class WatchHistoryBrowser extends Component
{
    use WithPagination;

    #[Url(as: 'type', except: 'all', history: true)]
    public string $type = 'all';

    #[Url(as: 'search', except: '', history: true)]
    public string $search = '';

    /**
     * Reset the pagination when filters are updated.
     */
    public function updated(string $property): void
    {
        if ($property === 'type' && ! in_array($this->type, ['all', 'movie', 'tv'], true)) {
            $this->type = 'all';
        }

        if (in_array($property, ['type', 'search'], true)) {
            $this->resetPage();
        }
    }

    /**
     * Render the component.
     */
    public function render()
    {
        $histories = $this->histories();

        return view('livewire.watch-history-browser', [
            'histories' => $histories,
            'metrics' => $this->metrics(),
        ])->layout('layouts.dashboard', [
            'title' => 'Watch history',
            'header' => 'Watch history',
            'subheader' => 'Revisit your recently streamed movies and series.',
            'navigation' => $this->navigation(),
        ]);
    }

    /**
     * Build the base query for the authenticated user's watch history.
     */
    protected function historyQuery(): Builder
    {
        return WatchHistory::query()
            ->with('watchable')
            ->forUser($this->user())
            ->when($this->type === 'movie', function (Builder $query): Builder {
                return $query->where('watchable_type', Movie::class);
            })
            ->when($this->type === 'tv', function (Builder $query): Builder {
                return $query->where('watchable_type', TvShow::class);
            })
            ->when($this->search !== '', function (Builder $query): Builder {
                $like = '%'.$this->search.'%';

                return $query->whereHasMorph('watchable', [Movie::class, TvShow::class], function (Builder $morphQuery, string $type) use ($like): void {
                    if ($type === Movie::class) {
                        $morphQuery->where(function (Builder $innerQuery) use ($like): void {
                            $innerQuery
                                ->where('title', 'like', $like)
                                ->orWhere('original_title', 'like', $like)
                                ->orWhere('slug', 'like', $like);
                        });

                        return;
                    }

                    $morphQuery->where(function (Builder $innerQuery) use ($like): void {
                        $innerQuery
                            ->where('name', 'like', $like)
                            ->orWhere('original_name', 'like', $like)
                            ->orWhere('slug', 'like', $like);
                    });
                });
            })
            ->orderByDesc('watched_at')
            ->orderByDesc('id');
    }

    /**
     * Retrieve the paginated watch history collection.
     */
    protected function histories(): LengthAwarePaginator
    {
        return $this->historyQuery()->paginate(perPage: 12, pageName: 'page');
    }

    /**
     * Aggregate quick metrics about the user's history.
     *
     * @return array{total:int, unique:int, last_watched_at:?\Illuminate\Support\Carbon}
     */
    protected function metrics(): array
    {
        $base = WatchHistory::query()->forUser($this->user());

        $total = (clone $base)->count();
        $unique = (clone $base)->select('watchable_type', 'watchable_id')->distinct()->count();
        $lastEntry = (clone $base)
            ->orderByDesc('watched_at')
            ->orderByDesc('id')
            ->first(['watched_at']);

        return [
            'total' => $total,
            'unique' => $unique,
            'last_watched_at' => $lastEntry?->watched_at,
        ];
    }

    /**
     * Build the navigation structure for the dashboard layout.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function navigation(): array
    {
        $locale = app()->getLocale();

        return [
            [
                'label' => 'Overview',
                'description' => 'Subscription status and key actions.',
                'href' => route('dashboard', ['locale' => $locale]),
                'active' => request()->routeIs('dashboard'),
            ],
            [
                'label' => 'Account settings',
                'description' => 'Update your profile and preferences.',
                'href' => route('account', ['locale' => $locale]),
                'active' => request()->routeIs('account'),
            ],
            [
                'label' => 'Watch history',
                'description' => 'Browse everything you have streamed.',
                'href' => route('account.watch-history', ['locale' => $locale]),
                'active' => request()->routeIs('account.watch-history'),
            ],
            [
                'label' => 'Manage subscription',
                'description' => 'Open the billing portal in a new tab.',
                'href' => route('billing.portal', ['locale' => $locale]),
                'target' => '_blank',
            ],
        ];
    }

    /**
     * Retrieve the authenticated user instance.
     */
    protected function user(): User
    {
        /** @var User $user */
        $user = Auth::user();

        return $user;
    }
}
