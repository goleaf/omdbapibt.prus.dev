<?php

namespace App\Livewire;

use App\Models\Movie;
use App\Models\Review;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use Livewire\Component;

class MovieDetail extends Component
{
    public const TABS = [
        'overview' => 'Overview',
        'credits' => 'Credits',
        'streaming' => 'Streaming',
        'trailers' => 'Trailers',
        'reviews' => 'Reviews',
        'translations' => 'Translations',
    ];

    public Movie $movie;

    public string $locale;

    public string $activeTab = 'overview';

    /**
     * @var array<int, string>
     */
    public array $genreNames = [];

    /**
     * @var array<int, string>
     */
    public array $languageNames = [];

    /**
     * @var array<int, string>
     */
    public array $countryNames = [];

    public function mount(Movie $movie): void
    {
        $relations = [];

        if (Schema::hasTable('genres')) {
            $relations[] = 'genres';
        }

        if (Schema::hasTable('languages')) {
            $relations[] = 'languages';
        }

        if (Schema::hasTable('countries')) {
            $relations[] = 'countries';
        }

        $this->movie = $relations !== [] ? $movie->loadMissing($relations) : $movie;

        $this->genreNames = Schema::hasTable('genres')
            ? $this->movie->genres->pluck('name')->filter()->values()->all()
            : [];

        $this->languageNames = Schema::hasTable('languages')
            ? $this->movie->languages->pluck('name')->filter()->values()->all()
            : [];

        $this->countryNames = Schema::hasTable('countries')
            ? $this->movie->countries->pluck('name')->filter()->values()->all()
            : [];
        $this->locale = request()->route('locale') ?? App::getLocale();

        App::setLocale($this->locale);
    }

    public function setTab(string $tab): void
    {
        if (array_key_exists($tab, self::TABS)) {
            $this->activeTab = $tab;
        }
    }

    public function getReviewsProperty(): Collection
    {
        $title = $this->translate($this->movie->title);

        return Review::query()
            ->when($title !== null, function ($query) use ($title): void {
                $query->where('movie_title', $title);
            })
            ->latest()
            ->limit(5)
            ->get();
    }

    public function translate(null|array|string $value, ?string $locale = null): ?string
    {
        if (is_array($value)) {
            $locale ??= $this->locale;

            if ($locale && Arr::has($value, $locale)) {
                $translated = Arr::get($value, $locale);

                return is_string($translated) ? $translated : null;
            }

            $fallback = config('app.fallback_locale');

            if ($fallback && Arr::has($value, $fallback)) {
                $fallbackValue = Arr::get($value, $fallback);

                return is_string($fallbackValue) ? $fallbackValue : null;
            }

            $first = Arr::first($value);

            return is_string($first) ? $first : null;
        }

        return $value !== null ? (string) $value : null;
    }

    public function getTabLabelsProperty(): array
    {
        return self::TABS;
    }

    public function embedUrlFor(array $trailer): ?string
    {
        $url = $trailer['url'] ?? null;

        if (! is_string($url) || $url === '') {
            return null;
        }

        if (isset($trailer['embed_url']) && is_string($trailer['embed_url'])) {
            return $trailer['embed_url'];
        }

        if (str_contains($url, 'youtube.com/watch')) {
            return str_replace('watch?v=', 'embed/', $url);
        }

        if (str_contains($url, 'youtu.be/')) {
            return str_replace('youtu.be/', 'www.youtube.com/embed/', $url);
        }

        return $url;
    }

    public function render(): View
    {
        $title = $this->translate($this->movie->title) ?? __('Movie detail');

        return view('livewire.movie-detail', [
            'tabLabels' => $this->tabLabels,
            'localizedTitle' => $title,
        ])->layout('layouts.app', [
            'title' => $title.' • '.config('app.name'),
        ]);
    }
}
