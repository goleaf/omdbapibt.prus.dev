<?php

namespace App\Livewire;

use Livewire\Component;

class TvShowList extends Component
{
    /**
     * Active filter selections keyed by filter name.
     */
    public array $filters = [
        'genre' => 'all',
        'status' => 'all',
        'language' => 'all',
        'popularity' => 'all',
    ];

    /**
     * Option lists for the available filters.
     */
    public array $genres = [];

    public array $statuses = [];

    public array $languages = [];

    public array $popularityRanges = [];

    public function mount(): void
    {
        $shows = $this->sampleShows();

        $this->genres = $this->uniqueValues($shows, 'genre');
        $this->statuses = $this->uniqueValues($shows, 'status');
        $this->languages = $this->uniqueValues($shows, 'language');

        $this->popularityRanges = [
            'all' => 'All popularity',
            '80_plus' => 'Trending (80+)',
            '50_79' => 'Buzzworthy (50-79)',
            'under_50' => 'Hidden gems (<50)',
        ];
    }

    public function updatedFilters($value, string $key): void
    {
        if ($value === null || $value === '') {
            data_set($this->filters, $key, 'all');
        }
    }

    public function getFilteredShowsProperty(): array
    {
        return collect($this->sampleShows())
            ->filter(function (array $show): bool {
                if ($this->filters['genre'] !== 'all' && $show['genre'] !== $this->filters['genre']) {
                    return false;
                }

                if ($this->filters['status'] !== 'all' && $show['status'] !== $this->filters['status']) {
                    return false;
                }

                if ($this->filters['language'] !== 'all' && $show['language'] !== $this->filters['language']) {
                    return false;
                }

                if ($this->filters['popularity'] !== 'all') {
                    $popularity = $show['popularity'];

                    if ($this->filters['popularity'] === '80_plus' && $popularity < 80) {
                        return false;
                    }

                    if ($this->filters['popularity'] === '50_79' && ($popularity < 50 || $popularity >= 80)) {
                        return false;
                    }

                    if ($this->filters['popularity'] === 'under_50' && $popularity >= 50) {
                        return false;
                    }
                }

                return true;
            })
            ->values()
            ->all();
    }

    public function render()
    {
        return view('livewire.tv-show-list', [
            'shows' => $this->filteredShows,
            'genres' => $this->genres,
            'statuses' => $this->statuses,
            'languages' => $this->languages,
            'popularityRanges' => $this->popularityRanges,
        ]);
    }

    private function uniqueValues(array $shows, string $key): array
    {
        return collect($shows)
            ->pluck($key)
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    private function sampleShows(): array
    {
        return [
            [
                'name' => 'The Expanse',
                'genre' => 'Science Fiction',
                'status' => 'Ended',
                'language' => 'English',
                'popularity' => 84.2,
                'poster' => 'https://image.tmdb.org/t/p/w500/bk3QKOPaSBeqheQmDXr7NEcJXbU.jpg',
                'first_air_date' => '2015',
                'seasons' => 6,
                'rating' => 8.6,
                'overview' => 'A hardened detective and a rogue captain uncover a vast conspiracy that threatens the fragile peace of the solar system.',
            ],
            [
                'name' => 'Dark',
                'genre' => 'Mystery',
                'status' => 'Ended',
                'language' => 'German',
                'popularity' => 77.8,
                'poster' => 'https://image.tmdb.org/t/p/w500/7q448EVOnuE3gVAx24krzO7SNXM.jpg',
                'first_air_date' => '2017',
                'seasons' => 3,
                'rating' => 8.4,
                'overview' => 'Four families become entangled in a time-travel conspiracy spanning several generations in the small German town of Winden.',
            ],
            [
                'name' => 'Arcane',
                'genre' => 'Animation',
                'status' => 'Returning Series',
                'language' => 'English',
                'popularity' => 89.9,
                'poster' => 'https://image.tmdb.org/t/p/w500/abzFR6uZMWLZq0cOLe7dO6A8t3U.jpg',
                'first_air_date' => '2021',
                'seasons' => 2,
                'rating' => 9.0,
                'overview' => 'Sisters Vi and Jinx find themselves on opposing sides of a brewing war between the utopian Piltover and the oppressed underground of Zaun.',
            ],
            [
                'name' => 'The Last of Us',
                'genre' => 'Drama',
                'status' => 'Returning Series',
                'language' => 'English',
                'popularity' => 95.1,
                'poster' => 'https://image.tmdb.org/t/p/w500/uKvVjHNqB5VmOrdxqAt2F7J78ED.jpg',
                'first_air_date' => '2023',
                'seasons' => 2,
                'rating' => 8.8,
                'overview' => 'Twenty years after modern civilization has been destroyed, a hardened survivor and a teenage girl embark on a harrowing journey.',
            ],
            [
                'name' => 'Money Heist',
                'genre' => 'Crime',
                'status' => 'Ended',
                'language' => 'Spanish',
                'popularity' => 71.6,
                'poster' => 'https://image.tmdb.org/t/p/w500/reEMJA1uzscCbkpeRJeTT2bjqUp.jpg',
                'first_air_date' => '2017',
                'seasons' => 5,
                'rating' => 8.2,
                'overview' => 'An enigmatic mastermind recruits a gang of criminals to pull off the biggest heist in recorded history.',
            ],
            [
                'name' => 'Hospital Playlist',
                'genre' => 'Drama',
                'status' => 'Ended',
                'language' => 'Korean',
                'popularity' => 48.3,
                'poster' => 'https://image.tmdb.org/t/p/w500/7LJJVeCEoFeADaDGqjJUqv6Dk38.jpg',
                'first_air_date' => '2020',
                'seasons' => 2,
                'rating' => 8.7,
                'overview' => 'Five doctors, friends since medical school, share the ups and downs of life at the same hospital in Seoul.',
            ],
            [
                'name' => 'Babylon Berlin',
                'genre' => 'Historical',
                'status' => 'Returning Series',
                'language' => 'German',
                'popularity' => 52.1,
                'poster' => 'https://image.tmdb.org/t/p/w500/2bXujXkzFDVhR9vqbS1JDFhkX1o.jpg',
                'first_air_date' => '2017',
                'seasons' => 4,
                'rating' => 8.1,
                'overview' => 'A police inspector uncovers political intrigue and criminal conspiracies in 1920s Berlin.',
            ],
            [
                'name' => 'Borgen',
                'genre' => 'Political',
                'status' => 'Ended',
                'language' => 'Danish',
                'popularity' => 43.7,
                'poster' => 'https://image.tmdb.org/t/p/w500/8aWlTXYlRVZFySUdkGFUTkOcJCX.jpg',
                'first_air_date' => '2010',
                'seasons' => 4,
                'rating' => 8.3,
                'overview' => 'A political drama chronicling the rise of Denmark’s first female prime minister and the sacrifices she must make to stay in power.',
            ],
        ];
    }
}
