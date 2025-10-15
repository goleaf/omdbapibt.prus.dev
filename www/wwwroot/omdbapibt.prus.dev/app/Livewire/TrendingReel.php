<?php

namespace App\Livewire;

use Livewire\Attributes\Computed;
use Livewire\Component;

class TrendingReel extends Component
{
    #[Computed]
    public function items(): array
    {
        return [
            [
                'title' => 'The Galactic Voyage',
                'media_type' => 'Movie',
                'poster' => 'https://images.unsplash.com/photo-1524985069026-dd778a71c7b4?auto=format&fit=crop&w=360&q=80',
                'rating' => 8.7,
                'genres' => ['Sci-Fi', 'Adventure'],
            ],
            [
                'title' => 'Eclipse Station',
                'media_type' => 'Series',
                'poster' => 'https://images.unsplash.com/photo-1524985069026-dd778a71c7b4?auto=format&fit=crop&w=360&q=80',
                'rating' => 8.3,
                'genres' => ['Thriller', 'Drama'],
            ],
            [
                'title' => 'Legends of Aurora',
                'media_type' => 'Movie',
                'poster' => 'https://images.unsplash.com/photo-1524985069026-dd778a71c7b4?auto=format&fit=crop&w=360&q=80',
                'rating' => 7.9,
                'genres' => ['Fantasy'],
            ],
            [
                'title' => 'Metro Pulse',
                'media_type' => 'Series',
                'poster' => 'https://images.unsplash.com/photo-1524985069026-dd778a71c7b4?auto=format&fit=crop&w=360&q=80',
                'rating' => 8.1,
                'genres' => ['Action', 'Crime'],
            ],
        ];
    }

    public function render()
    {
        return view('livewire.trending-reel');
    }
}
