<?php

namespace App\Livewire;

use Livewire\Attributes\Computed;
use Livewire\Component;

class MediaFilters extends Component
{
    public array $selected = [
        'type' => 'movies',
        'genre' => 'Science Fiction',
        'year' => '2024',
        'language' => 'English',
        'sort' => 'popularity.desc',
    ];

    #[Computed]
    public function genres(): array
    {
        return [
            'Action',
            'Adventure',
            'Comedy',
            'Drama',
            'Fantasy',
            'Science Fiction',
            'Thriller',
        ];
    }

    #[Computed]
    public function languages(): array
    {
        return ['English', 'Spanish', 'French', 'German', 'Japanese'];
    }

    #[Computed]
    public function years(): array
    {
        $currentYear = (int) now()->year;

        return collect(range($currentYear, $currentYear - 20))->map(fn ($year) => (string) $year)->all();
    }

    public function render()
    {
        return view('livewire.media-filters');
    }
}
