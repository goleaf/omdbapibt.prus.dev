<?php

namespace App\Livewire;

use App\Models\Movie;
use Livewire\Component;

class MovieDetailOverview extends Component
{
    public string $slug;

    public array $movie = [];

    public ?int $movieId = null;

    public function mount(string $slug): void
    {
        $this->slug = $slug;

        // Fake movie payload for layout scaffolding.
        $this->movie = [
            'id' => null,
            'title' => 'The Galactic Voyage',
            'tagline' => 'Where destiny meets the edge of the universe',
            'overview' => 'A daring pilot leads a crew of misfits on an impossible mission to chart the uncharted wormholes before a cosmic storm collapses the galaxy gate.',
            'poster' => 'https://images.unsplash.com/photo-1524985069026-dd778a71c7b4?auto=format&fit=crop&w=480&q=80',
            'runtime' => 142,
            'release_date' => '2024-06-10',
            'genres' => ['Science Fiction', 'Adventure'],
            'countries' => ['United States', 'Canada'],
            'languages' => ['English', 'French'],
            'cast' => ['Alexis Moon', 'Kenji Calderon', 'Priya Sol', 'Harlow Quinn'],
            'director' => 'Mira Thornton',
            'streaming' => [
                ['provider' => 'Nebula+', 'quality' => '4K'],
                ['provider' => 'CinePrime', 'quality' => 'HD'],
            ],
            'trailer_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
            'rating' => 8.7,
        ];

        $this->movieId = Movie::where('slug', $slug)->value('id');

        if ($this->movieId) {
            $this->movie['id'] = $this->movieId;
        }
    }

    public function render()
    {
        return view('livewire.movie-detail-overview');
    }
}
