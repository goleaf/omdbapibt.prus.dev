<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class MediaFilters extends Component
{
    public array $selected = [
        'type' => 'movies',
        'genre' => 'science_fiction',
        'year' => '2024',
        'language' => 'en',
        'sort' => 'popularity.desc',
    ];

    #[Computed]
    public function types(): array
    {
        return [
            'movies' => 'movies',
            'shows' => 'shows',
        ];
    }

    #[Computed]
    public function genres(): array
    {
        return [
            'action',
            'adventure',
            'comedy',
            'drama',
            'fantasy',
            'science_fiction',
            'thriller',
        ];
    }

    #[Computed]
    public function languages(): array
    {
        return ['en', 'es', 'fr', 'de', 'ja'];
    }

    #[Computed]
    public function years(): array
    {
        $currentYear = (int) now()->year;

        return collect(range($currentYear, $currentYear - 20))->map(fn ($year) => (string) $year)->all();
    }

    #[Computed]
    public function sorts(): array
    {
        return [
            'popularity.desc' => 'popularity',
            'vote_average.desc' => 'rating',
            'release_date.desc' => 'newest',
            'release_date.asc' => 'oldest',
        ];
    }

    public function render(): View
    {
        return view('livewire.media-filters');
    }
}
