<?php

namespace App\Livewire;

use App\Models\Movie;
use App\Models\TvShow;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Watchlist extends Component
{
    public ?int $movieId = null;

    public ?int $tvShowId = null;

    public bool $isSaved = false;

    public bool $isAuthenticated = false;

    /**
     * @var array{movies: list<array<string, mixed>>, shows: list<array<string, mixed>>}
     */
    public array $items = [
        'movies' => [],
        'shows' => [],
    ];

    public function mount(?int $movieId = null, ?int $tvShowId = null): void
    {
        $this->movieId = $movieId;
        $this->tvShowId = $tvShowId;
        $this->isAuthenticated = Auth::check();

        if ($this->isToggleMode()) {
            $this->isSaved = $this->determineSavedState();
        } elseif ($this->isAuthenticated) {
            $this->loadWatchlist();
        }
    }

    public function toggle(): void
    {
        if (! $this->isAuthenticated) {
            session()->flash('error', 'Sign in to save titles to your watchlist.');

            return;
        }

        if (! $this->isToggleMode()) {
            return;
        }

        $user = $this->user();

        if ($this->movieId !== null) {
            $this->toggleMovie($user);
        } elseif ($this->tvShowId !== null) {
            $this->toggleTvShow($user);
        }

        $this->isSaved = $this->determineSavedState();
        $this->dispatch('watchlist-updated');
    }

    public function remove(string $type, int $identifier): void
    {
        if (! $this->isAuthenticated) {
            session()->flash('error', 'Sign in to manage your watchlist.');

            return;
        }

        $user = $this->user();

        if ($type === 'movie') {
            $user->watchlistedMovies()->detach($identifier);
        } elseif ($type === 'tv') {
            $user->watchlistedTvShows()->detach($identifier);
        }

        $this->loadWatchlist();
        $this->dispatch('watchlist-updated');
    }

    protected function toggleMovie(User $user): void
    {
        $movie = Movie::find($this->movieId);

        if (! $movie) {
            session()->flash('error', 'We could not find this movie.');

            return;
        }

        if ($user->watchlistedMovies()->whereKey($movie->getKey())->exists()) {
            $user->watchlistedMovies()->detach($movie->getKey());
            session()->flash('status', 'Removed from your watchlist.');

            return;
        }

        $user->watchlistedMovies()->syncWithoutDetaching([$movie->getKey()]);
        session()->flash('status', 'Added to your watchlist.');
    }

    protected function toggleTvShow(User $user): void
    {
        $tvShow = TvShow::find($this->tvShowId);

        if (! $tvShow) {
            session()->flash('error', 'We could not find this series.');

            return;
        }

        if ($user->watchlistedTvShows()->whereKey($tvShow->getKey())->exists()) {
            $user->watchlistedTvShows()->detach($tvShow->getKey());
            session()->flash('status', 'Removed from your watchlist.');

            return;
        }

        $user->watchlistedTvShows()->syncWithoutDetaching([$tvShow->getKey()]);
        session()->flash('status', 'Added to your watchlist.');
    }

    protected function loadWatchlist(): void
    {
        if (! $this->isAuthenticated) {
            $this->items = [
                'movies' => [],
                'shows' => [],
            ];

            return;
        }

        $user = $this->user();

        $movies = $user->watchlistedMovies()
            ->withPivot('created_at')
            ->orderByDesc('user_watchlist.created_at')
            ->get(['id', 'title', 'poster_path', 'slug', 'release_date']);

        $shows = $user->watchlistedTvShows()
            ->withPivot('created_at')
            ->orderByDesc('user_watchlist.created_at')
            ->get(['id', 'name', 'poster_path', 'slug', 'first_air_date']);

        $this->items = [
            'movies' => $movies->map(function (Movie $movie): array {
                $title = $movie->title;

                if (is_array($title)) {
                    $title = $title['en'] ?? reset($title) ?? 'Untitled';
                }

                return [
                    'id' => $movie->getKey(),
                    'title' => $title,
                    'slug' => $movie->slug,
                    'poster' => $movie->poster_path,
                    'year' => $movie->release_date ? $movie->release_date->format('Y') : null,
                ];
            })->all(),
            'shows' => $shows->map(function (TvShow $show): array {
                return [
                    'id' => $show->getKey(),
                    'title' => $show->name,
                    'slug' => $show->slug,
                    'poster' => $show->poster_path,
                    'year' => $show->first_air_date ? $show->first_air_date->format('Y') : null,
                ];
            })->all(),
        ];
    }

    protected function determineSavedState(): bool
    {
        if (! $this->isAuthenticated || ! $this->isToggleMode()) {
            return false;
        }

        $user = $this->user();

        if ($this->movieId !== null) {
            return $user->watchlistedMovies()->whereKey($this->movieId)->exists();
        }

        if ($this->tvShowId !== null) {
            return $user->watchlistedTvShows()->whereKey($this->tvShowId)->exists();
        }

        return false;
    }

    protected function isToggleMode(): bool
    {
        return $this->movieId !== null || $this->tvShowId !== null;
    }

    protected function user(): User
    {
        $user = Auth::user();

        abort_unless($user instanceof User, 403);

        return $user;
    }

    public function render()
    {
        if (! $this->isToggleMode() && $this->isAuthenticated) {
            $this->loadWatchlist();
        }

        return view('livewire.watchlist', [
            'toggleMode' => $this->isToggleMode(),
        ]);
    }
}
