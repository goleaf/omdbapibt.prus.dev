<?php

namespace App\Livewire\Landing;

use App\Models\Movie;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class CatalogBrowser extends Component
{
    use WithPagination;

    #[Url(as: 'collection', except: null, history: true)]
    public ?string $activeCollection = null;

    public array $lazyMovies = [];

    public bool $hasMorePages = false;

    public int $totalResults = 0;

    public array $activeMeta = [];

    protected int $perPage = 9;

    protected string $pageName = 'catalogPage';

    /**
     * Initialise the component with the default curated collection.
     */
    public function mount(): void
    {
        if ($this->ensureActiveCollection()) {
            $this->resetForActiveCollection();
        }
    }

    /**
     * Switch to a different curated collection.
     */
    public function setCollection(string $collectionKey): void
    {
        $this->activeCollection = $collectionKey;
    }

    /**
     * React when the active collection changes via the URL binding.
     */
    public function updatedActiveCollection(): void
    {
        if ($this->ensureActiveCollection()) {
            $this->resetForActiveCollection();
        }
    }

    /**
     * Load the next page of results for infinite scrolling.
     */
    public function loadMore(): void
    {
        if (! $this->hasMorePages) {
            return;
        }

        $this->setPage($this->getPage($this->pageName) + 1, $this->pageName);
        $this->appendCurrentPage();
    }

    /**
     * Reset pagination state when the active collection changes.
     */
    protected function resetForActiveCollection(): void
    {
        $this->resetPage($this->pageName);
        $this->lazyMovies = [];
        $this->appendCurrentPage();
    }

    /**
     * Append the current page of movies to the lazy-loaded buffer.
     */
    protected function appendCurrentPage(): void
    {
        $paginator = $this->buildPaginator();

        $this->hasMorePages = $paginator->hasMorePages();
        $this->totalResults = $paginator->total();
        $this->activeMeta = $this->collectionMeta();

        $items = collect($paginator->items())->map(function (Movie $movie): array {
            $releaseYear = $movie->release_date?->format('Y') ?? ($movie->year ? (string) $movie->year : null);

            return [
                'id' => $movie->id,
                'title' => $movie->title,
                'slug' => $movie->slug,
                'poster_path' => $movie->poster_path,
                'tagline' => $movie->tagline,
                'release_year' => $releaseYear,
                'vote_average' => $movie->vote_average ? number_format((float) $movie->vote_average, 1) : null,
                'genres' => $movie->genres->pluck('name')->take(2)->implode(' • '),
            ];
        })->all();

        $this->lazyMovies = collect($this->lazyMovies)
            ->merge($items)
            ->unique('id')
            ->values()
            ->all();
    }

    /**
     * Build a paginator instance for the current query state.
     */
    protected function buildPaginator(): LengthAwarePaginator
    {
        return $this->buildQuery()->paginate(
            perPage: $this->perPage,
            page: $this->getPage($this->pageName),
            pageName: $this->pageName,
        );
    }

    /**
     * Construct the base movie query for the active collection.
     */
    protected function buildQuery(): Builder
    {
        $collection = $this->collections()[$this->activeCollection] ?? null;

        $query = Movie::query()
            ->select([
                'id',
                'title',
                'slug',
                'poster_path',
                'tagline',
                'release_date',
                'year',
                'vote_average',
                'popularity',
                'created_at',
            ])
            ->with([
                'genres:id,name,slug',
                'languages:id,code',
            ]);

        if ($collection === null) {
            return $query->whereRaw('1 = 0');
        }

        $query->whereNotNull('poster_path');

        $featured = Arr::get($collection, 'featured_slugs', []);

        if ($featured !== []) {
            $query->whereIn('slug', $featured);

            $placeholders = implode(', ', array_fill(0, count($featured), '?'));

            $query->orderByRaw('FIELD(slug, '.$placeholders.')', $featured);
        }

        $genreSlugs = Arr::get($collection, 'genre_slugs', []);

        if ($genreSlugs !== []) {
            $query->whereHas('genres', function (Builder $builder) use ($genreSlugs): void {
                $builder->whereIn('slug', $genreSlugs);
            });
        }

        $languageCodes = Arr::get($collection, 'language_codes', []);

        if ($languageCodes !== []) {
            $query->whereHas('languages', function (Builder $builder) use ($languageCodes): void {
                $builder->whereIn('code', $languageCodes);
            });
        }

        if ($minimumRating = Arr::get($collection, 'minimum_rating')) {
            $query->where('vote_average', '>=', (float) $minimumRating);
        }

        if ($minimumPopularity = Arr::get($collection, 'minimum_popularity')) {
            $query->where('popularity', '>=', (float) $minimumPopularity);
        }

        if ($days = Arr::get($collection, 'released_within_days')) {
            $cutoff = Carbon::now()->subDays((int) $days);

            $query->where(function (Builder $builder) use ($cutoff): void {
                $builder->whereDate('release_date', '>=', $cutoff->toDateString())
                    ->orWhere(function (Builder $inner) use ($cutoff): void {
                        $inner->whereNull('release_date')
                            ->whereDate('created_at', '>=', $cutoff->toDateString());
                    });
            });
        }

        if ($year = Arr::get($collection, 'released_after_year')) {
            $query->where(function (Builder $builder) use ($year): void {
                $builder->where('year', '>=', (int) $year)
                    ->orWhereYear('release_date', '>=', (int) $year);
            });
        }

        $sort = Arr::get($collection, 'sort', []);

        if ($sort === []) {
            $query->orderByDesc('popularity')
                ->orderByDesc('release_date')
                ->orderBy('title');
        } else {
            foreach ($sort as $column => $direction) {
                $query->orderBy($column, $direction);
            }
        }

        return $query;
    }

    /**
     * Ensure there is a valid collection selected.
     */
    protected function ensureActiveCollection(): bool
    {
        $collections = $this->collections();

        if ($collections === []) {
            $this->activeCollection = null;
            $this->activeMeta = [
                'label' => 'Curated catalog',
                'tagline' => null,
                'description' => 'Define curated collections in config/catalog.php to populate this module.',
                'key' => null,
            ];
            $this->lazyMovies = [];
            $this->hasMorePages = false;
            $this->totalResults = 0;

            return false;
        }

        if ($this->activeCollection === null || ! array_key_exists($this->activeCollection, $collections)) {
            $this->activeCollection = array_key_first($collections);
        }

        return true;
    }

    /**
     * Retrieve the curated collections configuration.
     */
    protected function collections(): array
    {
        return config('catalog.collections', []);
    }

    /**
     * Get metadata for the active collection.
     */
    protected function collectionMeta(): array
    {
        $collection = $this->collections()[$this->activeCollection] ?? [];

        return [
            'label' => Arr::get($collection, 'label', 'Curated catalog'),
            'tagline' => Arr::get($collection, 'tagline'),
            'description' => Arr::get($collection, 'description'),
            'key' => $this->activeCollection,
        ];
    }

    public function render(): View
    {
        return view('livewire.landing.catalog-browser', [
            'collections' => $this->collections(),
            'perPage' => $this->perPage,
        ]);
    }
}
