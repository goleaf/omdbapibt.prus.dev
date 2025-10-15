<?php

namespace App\Livewire;

use Livewire\Component;

class ShowDetailOverview extends Component
{
    public string $slug;

    public array $show = [];

    public function mount(string $slug): void
    {
        $this->slug = $slug;

        $this->show = [
            'name' => 'Eclipse Station',
            'tagline' => 'Nightfall is just the beginning',
            'overview' => 'An orbital security chief uncovers an interstellar conspiracy while protecting a station caught between warring factions.',
            'poster' => 'https://images.unsplash.com/photo-1581905764498-6014f49e91d5?auto=format&fit=crop&w=480&q=80',
            'episode_run_time' => 55,
            'first_air_date' => '2023-10-18',
            'last_air_date' => '2024-05-22',
            'seasons' => 2,
            'episodes' => 16,
            'genres' => ['Thriller', 'Drama'],
            'languages' => ['English'],
            'cast' => ['Devon Kade', 'Suri Allen', 'Lina Ortiz', 'Mason Reeve'],
            'creators' => ['Jules Renaud'],
            'streaming' => [
                ['provider' => 'Nebula+', 'quality' => '4K'],
                ['provider' => 'StreamSphere', 'quality' => 'HD'],
            ],
            'trailer_url' => 'https://www.youtube.com/embed/V-_O7nl0Ii0',
            'rating' => 8.3,
        ];
    }

    public function render()
    {
        return view('livewire.show-detail-overview');
    }
}
