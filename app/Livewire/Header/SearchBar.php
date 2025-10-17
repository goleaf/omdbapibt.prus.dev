<?php

namespace App\Livewire\Header;

use App\Models\Movie;
use App\Models\Person;
use App\Models\TvShow;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;

class SearchBar extends Component
{
    private const MIN_QUERY_LENGTH = 2;

    public string $query = '';

    public array $results = [];

    public array $flatResults = [];

    public int $activeIndex = -1;

    public bool $showResults = false;

    public bool $isLoading = false;

    protected int $limit = 5;

    /**
     * @var array<string, bool>
     */
    protected array $fullTextCache = [];

    public function updatedQuery(): void
    {
        $this->activeIndex = -1;

        if (strlen($this->query) < self::MIN_QUERY_LENGTH) {
            $this->reset(['results', 'flatResults', 'showResults']);

            return;
        }

        $this->isLoading = true;
        $this->search();
        $this->showResults = true;
        $this->isLoading = false;
    }

    protected function search(): void
    {
        $term = trim($this->query);

        if (Str::length($term) < self::MIN_QUERY_LENGTH) {
            $this->results = $this->emptyResults();
            $this->flatResults = [];

            return;
        }

        $movies = $this->formatMovies($this->searchMovies($term));
        $shows = $this->formatTvShows($this->searchTvShows($term));
        $people = $this->formatPeople($this->searchPeople($term));

        $this->storeResults($movies, $shows, $people);
    }

    protected function storeResults(array $movies, array $shows, array $people): void
    {
        $grouped = [
            'movies' => array_values($movies),
            'shows' => array_values($shows),
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

    public function highlightNext(): void
    {
        if (empty($this->flatResults)) {
            return;
        }

        $this->showResults = true;
        $this->activeIndex = ($this->activeIndex + 1) % count($this->flatResults);
    }

    public function highlightPrevious(): void
    {
        if (empty($this->flatResults)) {
            return;
        }

        $this->showResults = true;
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
            ->orderBy('name')
            ->whereLocalizedNameLike($term);

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
                'year' => optional($movie->release_date)->format('Y'),
                'poster' => $movie->poster_path,
                'url' => $movie->slug ? localized_route('movies.show', ['movie' => $movie->slug]) : null,
            ];
        })->all();
    }

    protected function formatTvShows(Collection $shows): array
    {
        return $shows->map(function (TvShow $show): array {
            return [
                'id' => $show->id,
                'title' => $show->localizedName(),
                'year' => optional($show->first_air_date)->format('Y'),
                'poster' => $show->poster_path,
                'url' => $show->slug ? localized_route('tv.show', ['show' => $show->slug]) : null,
            ];
        })->all();
    }

    protected function formatPeople(Collection $people): array
    {
        return $people->map(function (Person $person): array {
            return [
                'id' => $person->id,
                'name' => $person->name,
                'department' => $person->known_for_department,
                'poster' => $person->profile_path,
                'url' => $person->slug ? localized_route('people.show', ['person' => $person->slug]) : null,
            ];
        })->all();
    }

    protected function emptyResults(): array
    {
        return [
            'movies' => [],
            'shows' => [],
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

    public function clear(): void
    {
        $this->reset(['query', 'results', 'flatResults', 'showResults', 'activeIndex']);
    }

    #[On('focusSearch')]
    public function focusSearch(): void
    {
        $this->dispatch('focus-search-input');
    }

    #[On('closeAllDropdowns')]
    public function closeDropdowns(): void
    {
        $this->showResults = false;
        $this->activeIndex = -1;
    }

    public function render()
    {
        return view('livewire.header.search-bar');
    }
}
