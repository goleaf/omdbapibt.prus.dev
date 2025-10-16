<?php

namespace App\Livewire;

use App\Models\Movie;
use App\Models\Person;
use App\Models\TvShow;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Component;

class SearchGlobal extends Component
{
    private const MIN_QUERY_LENGTH = 2;

    public string $query = '';

    /**
     * @var array<string, array<int, array<string, mixed>>>
     */
    public array $results = [];

    /**
     * @var array<int, array<string, mixed>>
     */
    public array $flatResults = [];

    public int $activeIndex = -1;

    public bool $isOpen = false;

    protected int $limit = 5;

    /**
     * @var array<string, bool>
     */
    protected array $fullTextCache = [];

    protected const CATEGORY_LABELS = [
        'movies' => 'Movies',
        'tvShows' => 'TV Shows',
        'people' => 'People',
    ];

    public function mount(): void
    {
        $this->results = $this->emptyResults();
        $this->flatResults = [];
    }

    public function updatedQuery(): void
    {
        $this->activeIndex = -1;
        $this->performSearch();
    }

    public function openResults(): void
    {
        if ($this->hasResults || Str::length(trim($this->query)) >= self::MIN_QUERY_LENGTH) {
            $this->isOpen = true;
        }
    }

    public function closeResults(): void
    {
        $this->isOpen = false;
        $this->activeIndex = -1;
    }

    public function clear(): void
    {
        $this->reset(['query']);
        $this->results = $this->emptyResults();
        $this->flatResults = [];
        $this->activeIndex = -1;
        $this->isOpen = false;
    }

    public function highlightNext(): void
    {
        if (empty($this->flatResults)) {
            return;
        }

        $this->isOpen = true;

        $this->activeIndex = ($this->activeIndex + 1) % count($this->flatResults);
    }

    public function highlightPrevious(): void
    {
        if (empty($this->flatResults)) {
            return;
        }

        $this->isOpen = true;

        $this->activeIndex = $this->activeIndex <= 0
            ? count($this->flatResults) - 1
            : $this->activeIndex - 1;
    }

    public function openActiveResult(): mixed
    {
        $active = $this->flatResults[$this->activeIndex] ?? null;

        if (! $active || empty($active['url'])) {
            return null;
        }

        return redirect()->to($active['url']);
    }

    public function getHasResultsProperty(): bool
    {
        return collect($this->results)
            ->flatten(1)
            ->isNotEmpty();
    }

    public function render(): View
    {
        return view('livewire.search-global', [
            'categoryLabels' => self::CATEGORY_LABELS,
        ]);
    }

    protected function performSearch(): void
    {
        $term = trim($this->query);

        if (Str::length($term) < self::MIN_QUERY_LENGTH) {
            $this->results = $this->emptyResults();
            $this->flatResults = [];
            $this->isOpen = false;

            return;
        }

        $movies = $this->formatMovies($this->searchMovies($term));
        $tvShows = $this->formatTvShows($this->searchTvShows($term));
        $people = $this->formatPeople($this->searchPeople($term));

        $this->storeResults($movies, $tvShows, $people);
        $this->isOpen = true;
    }

    protected function searchMovies(string $term): Collection
    {
        $query = Movie::query()
            ->select(['id', 'title', 'slug', 'release_date', 'poster_path', 'popularity'])
            ->orderByDesc('popularity')
            ->orderBy('title');

        $query = $this->applySearch($query, 'movies', 'title', $term);

        return $query->limit($this->limit)->get();
    }

    protected function searchTvShows(string $term): Collection
    {
        $query = TvShow::query()
            ->select(['id', 'name', 'name_translations', 'slug', 'first_air_date', 'poster_path', 'popularity'])
            ->orderByDesc('popularity')
            ->orderBy('name');

        $query = $this->applySearch($query, 'tv_shows', 'name', $term);

        return $query->limit($this->limit)->get();
    }

    protected function searchPeople(string $term): Collection
    {
        $query = Person::query()
            ->select(['id', 'name', 'slug', 'known_for_department', 'profile_path', 'popularity'])
            ->orderByDesc('popularity')
            ->orderBy('name');

        $query = $this->applySearch($query, 'people', 'name', $term);

        return $query->limit($this->limit)->get();
    }

    protected function formatMovies(Collection $movies): array
    {
        return $movies->map(function (Movie $movie): array {
            return [
                'id' => $movie->id,
                'title' => $movie->localizedTitle(),
                'subtitle' => optional($movie->release_date)->format('Y'),
                'poster' => $movie->poster_path,
                'url' => $movie->slug ? url('/movies/'.$movie->slug) : null,
            ];
        })->all();
    }

    protected function formatTvShows(Collection $shows): array
    {
        return $shows->map(function (TvShow $show): array {
            return [
                'id' => $show->id,
                'title' => $show->localizedName(),
                'subtitle' => optional($show->first_air_date)->format('Y'),
                'poster' => $show->poster_path,
                'url' => $show->slug ? url('/tv/'.$show->slug) : null,
            ];
        })->all();
    }

    protected function formatPeople(Collection $people): array
    {
        return $people->map(function (Person $person): array {
            return [
                'id' => $person->id,
                'title' => $person->name,
                'subtitle' => $person->known_for_department,
                'poster' => $person->profile_path,
                'url' => $person->slug ? url('/people/'.$person->slug) : null,
            ];
        })->all();
    }

    protected function storeResults(array $movies, array $tvShows, array $people): void
    {
        $grouped = [
            'movies' => array_values($movies),
            'tvShows' => array_values($tvShows),
            'people' => array_values($people),
        ];

        $flat = [];

        foreach ($grouped as $group => $items) {
            foreach ($items as $index => $item) {
                $item['index'] = count($flat);
                $item['group'] = $group;
                $flat[] = $item;
                $grouped[$group][$index] = $item;
            }
        }

        $this->results = $grouped;
        $this->flatResults = $flat;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    protected function emptyResults(): array
    {
        return [
            'movies' => [],
            'tvShows' => [],
            'people' => [],
        ];
    }

    protected function applySearch(Builder $query, string $table, string $column, string $term): Builder
    {
        if ($this->supportsFullText($query, $table, $column)) {
            return $query->whereFullText($column, $term);
        }

        return $query->where($column, 'LIKE', '%'.$this->escapeLike($term).'%');
    }

    protected function supportsFullText(Builder $builder, string $table, string $column): bool
    {
        $connection = $builder->getConnection();
        $driver = $connection->getDriverName();

        if (! in_array($driver, ['mysql'])) {
            return false;
        }

        $cacheKey = $driver.'|'.$connection->getTablePrefix().$table.'|'.$column;

        if (array_key_exists($cacheKey, $this->fullTextCache)) {
            return $this->fullTextCache[$cacheKey];
        }

        $tableName = $connection->getTablePrefix().$table;
        $escaped = '`'.str_replace('`', '``', $tableName).'`';

        try {
            $indexes = $connection->select('SHOW INDEX FROM '.$escaped);
        } catch (\Throwable $exception) {
            return $this->fullTextCache[$cacheKey] = false;
        }

        foreach ($indexes as $index) {
            $type = strtolower($index->Index_type ?? $index->index_type ?? '');

            if ($type !== 'fulltext') {
                continue;
            }

            $columnName = $index->Column_name ?? $index->column_name ?? null;

            if ($columnName === $column) {
                return $this->fullTextCache[$cacheKey] = true;
            }
        }

        return $this->fullTextCache[$cacheKey] = false;
    }

    protected function escapeLike(string $value): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $value);
    }
}
