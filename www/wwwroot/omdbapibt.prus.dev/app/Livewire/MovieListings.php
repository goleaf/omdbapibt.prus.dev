<?php

namespace App\Livewire;

use Livewire\Attributes\Computed;
use Livewire\Component;

class MovieListings extends Component
{
    public int $page = 1;

    public int $perPage = 12;

    #[Computed]
    public function allMovies(): array
    {
        $movies = config('movies.catalog', []);

        return array_map(static function (array $movie): array {
            $defaults = [
                'title' => 'Untitled',
                'year' => null,
                'genres' => [],
                'overview' => null,
                'rating' => null,
                'poster' => null,
                'runtime' => null,
            ];

            return array_merge($defaults, $movie);
        }, $movies);
    }

    #[Computed]
    public function totalMovies(): int
    {
        return count($this->allMovies());
    }

    #[Computed]
    public function maxPage(): int
    {
        if ($this->perPage <= 0) {
            return 1;
        }

        return (int) max(1, ceil($this->totalMovies() / $this->perPage));
    }

    #[Computed]
    public function visibleMovies(): array
    {
        $take = $this->perPage * $this->page;

        return array_slice($this->allMovies(), 0, $take);
    }

    #[Computed]
    public function pageNumbers(): array
    {
        $pages = $this->maxPage();
        $current = $this->page;
        $window = 2;
        $numbers = [1, $pages, $current];

        for ($i = $current - $window; $i <= $current + $window; $i++) {
            if ($i > 1 && $i < $pages) {
                $numbers[] = $i;
            }
        }

        $numbers = array_values(array_unique(array_filter($numbers, static function ($page) use ($pages) {
            return $page >= 1 && $page <= $pages;
        })));

        sort($numbers);

        return $numbers;
    }

    public function loadMore(): void
    {
        if ($this->page >= $this->maxPage()) {
            return;
        }

        $this->page++;
    }

    public function goToPage(int $page): void
    {
        $page = max(1, min($page, $this->maxPage()));

        if ($page === $this->page) {
            return;
        }

        $this->page = $page;
    }

    public function render()
    {
        $start = ($this->page - 1) * $this->perPage + 1;
        $end = min($this->page * $this->perPage, $this->totalMovies());

        return view('livewire.movie-listings', [
            'visibleRange' => $this->totalMovies() === 0 ? null : [$start, $end],
        ]);
    }
}
