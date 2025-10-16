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

    public string $locale = '';

    public array $movieLinks = [];

    public array $showLinks = [];

    public int $movieCount = 0;

    public int $showCount = 0;

    public int $summaryCount = 0;

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
        $this->locale = app()->getLocale();

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
            $this->movieLinks = [];
            $this->showLinks = [];
            $this->movieCount = 0;
            $this->showCount = 0;
            $this->summaryCount = 0;

            return;
        }

        $user = $this->user();

        $movies = $user->watchlistedMovies()
            ->withPivot('created_at')
            ->orderByDesc('user_watchlist.created_at')
            ->get(['movies.id', 'title', 'poster_path', 'slug', 'release_date']);

        $shows = $user->watchlistedTvShows()
            ->withPivot('created_at')
            ->orderByDesc('user_watchlist.created_at')
            ->get(['tv_shows.id', 'name', 'name_translations', 'poster_path', 'slug', 'first_air_date']);

        $this->items = [
            'movies' => $movies->map(function (Movie $movie): array {
                return [
                    'id' => $movie->getKey(),
                    'title' => $movie->localizedTitle(),
                    'slug' => $movie->slug,
                    'poster' => $movie->poster_path,
                    'year' => $movie->release_date ? $movie->release_date->format('Y') : null,
                ];
            })->all(),
            'shows' => $shows->map(function (TvShow $show): array {
                return [
                    'id' => $show->getKey(),
                    'title' => $this->resolveShowTitle($show),
                    'slug' => $show->slug,
                    'poster' => $show->poster_path,
                    'year' => $show->first_air_date ? $show->first_air_date->format('Y') : null,
                ];
            })->all(),
        ];

        $this->movieCount = count($this->items['movies']);
        $this->showCount = count($this->items['shows']);
        $this->summaryCount = $this->movieCount + $this->showCount;

        $this->movieLinks = collect($this->items['movies'])
            ->mapWithKeys(function (array $movie): array {
                $url = $movie['slug']
                    ? route('movies.show', [
                        'locale' => $this->locale,
                        'movie' => $movie['slug'],
                    ])
                    : '#';

                return [$movie['id'] => $url];
            })
            ->all();

        $this->showLinks = collect($this->items['shows'])
            ->mapWithKeys(function (array $show): array {
                $url = $show['slug']
                    ? route('shows.show', [
                        'locale' => $this->locale,
                        'slug' => $show['slug'],
                    ])
                    : '#';

                return [$show['id'] => $url];
            })
            ->all();
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

    protected function resolveShowTitle(TvShow $show): string
    {
        $translations = $show->name_translations;

        if (is_array($translations)) {
            if ($this->locale !== ''
                && isset($translations[$this->locale])
                && is_string($translations[$this->locale])
                && $translations[$this->locale] !== '') {
                return $translations[$this->locale];
            }

            $fallbackLocale = config('app.fallback_locale');

            if ($fallbackLocale
                && isset($translations[$fallbackLocale])
                && is_string($translations[$fallbackLocale])
                && $translations[$fallbackLocale] !== '') {
                return $translations[$fallbackLocale];
            }

            if (isset($translations['en']) && is_string($translations['en']) && $translations['en'] !== '') {
                return $translations['en'];
            }

            foreach ($translations as $value) {
                if (is_string($value) && $value !== '') {
                    return $value;
                }
            }
        }

        if (is_string($show->name) && $show->name !== '') {
            return $show->name;
        }

        if (is_string($show->original_name) && $show->original_name !== '') {
            return $show->original_name;
        }

        return 'Untitled series';
    }

    public function render()
    {
        if (! $this->isToggleMode() && $this->isAuthenticated) {
            $this->loadWatchlist();
        }

        return view('livewire.watchlist', [
            'toggleMode' => $this->isToggleMode(),
            'locale' => $this->locale,
            'movieLinks' => $this->movieLinks,
            'showLinks' => $this->showLinks,
            'movieCount' => $this->movieCount,
            'showCount' => $this->showCount,
            'summaryCount' => $this->summaryCount,
        ]);
    }
}
