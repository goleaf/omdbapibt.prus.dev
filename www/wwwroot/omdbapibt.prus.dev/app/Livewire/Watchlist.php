<?php

namespace App\Livewire;

use App\Models\Movie;
use App\Models\TvShow;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Watchlist extends Component
{
    /**
     * @var string|null
     */
    public ?string $watchableType = null;

    /**
     * @var int|null
     */
    public ?int $watchableId = null;

    public bool $showList = true;

    public bool $inWatchlist = false;

    public bool $isAuthenticated = false;

    protected $listeners = ['watchlistUpdated' => '$refresh'];

    public function mount(?string $watchableType = null, ?int $watchableId = null, bool $showList = true): void
    {
        $this->watchableType = $watchableType;
        $this->watchableId = $watchableId;
        $this->showList = $showList;
        $this->isAuthenticated = Auth::check();

        if ($this->watchableType !== null && $this->watchableId !== null && $this->isAuthenticated) {
            $this->inWatchlist = $this->user()?->isInWatchlist($this->watchableType, $this->watchableId) ?? false;
        }
    }

    public function toggle(): void
    {
        if ($this->watchableType === null || $this->watchableId === null) {
            return;
        }

        $user = $this->user();

        if ($user === null) {
            return;
        }

        if ($user->isInWatchlist($this->watchableType, $this->watchableId)) {
            $user->removeFromWatchlist($this->watchableType, $this->watchableId);
            $this->inWatchlist = false;
        } else {
            $user->addToWatchlist($this->watchableType, $this->watchableId);
            $this->inWatchlist = true;
        }

        $this->dispatch('watchlistUpdated');
    }

    public function removeItem(string $type, int $id): void
    {
        $user = $this->user();

        if ($user === null) {
            return;
        }

        $user->removeFromWatchlist($type, $id);
        $this->dispatch('watchlistUpdated');
    }

    #[Computed]
    public function items(): Collection
    {
        $user = $this->user();

        if ($user === null || $this->showList === false) {
            return collect();
        }

        $items = collect();

        $movies = $user->watchlistMovies()->get()->map(function (Movie $movie) {
            return [
                'id' => $movie->id,
                'title' => $movie->display_title,
                'type' => 'movie',
                'type_label' => 'Movie',
                'poster_path' => $movie->poster_path,
                'added_at' => $movie->pivot?->created_at,
            ];
        });

        $items = $items->merge($movies);

        if (Schema::hasTable('tv_shows')) {
            $tvShows = $user->watchlistTvShows()->get()->map(function (TvShow $show) {
                return [
                    'id' => $show->id,
                    'title' => $show->display_title,
                    'type' => 'tv_show',
                    'type_label' => 'TV Show',
                    'poster_path' => $show->poster_path,
                    'added_at' => $show->pivot?->created_at,
                ];
            });

            $items = $items->merge($tvShows);
        }

        return $items
            ->sortByDesc(fn (array $item) => $item['added_at'])
            ->values();
    }

    public function render()
    {
        $this->isAuthenticated = Auth::check();

        if ($this->watchableType !== null && $this->watchableId !== null) {
            $this->inWatchlist = $this->user()?->isInWatchlist($this->watchableType, $this->watchableId) ?? false;
        }

        return view('livewire.watchlist');
    }

    private function user(): ?Authenticatable
    {
        return Auth::user();
    }
}
