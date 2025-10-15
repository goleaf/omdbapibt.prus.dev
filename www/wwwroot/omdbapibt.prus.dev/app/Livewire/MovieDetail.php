<?php

namespace App\Livewire;

use App\Models\Movie;
use Illuminate\Support\Arr;
use Livewire\Attributes\Url;
use Livewire\Component;

class MovieDetail extends Component
{
    public const DEFAULT_TAB = 'overview';

    public Movie $movie;

    #[Url(as: 'tab')]
    public string $activeTab = self::DEFAULT_TAB;

    public array $tabOrder = [
        'overview',
        'cast',
        'crew',
        'streaming',
        'trailers',
        'reviews',
        'translations',
    ];

    public function mount(Movie $movie): void
    {
        $this->movie = $movie;

        if (! in_array($this->activeTab, $this->tabOrder, true)) {
            $this->activeTab = self::DEFAULT_TAB;
        }
    }

    public function setTab(string $tab): void
    {
        if (! in_array($tab, $this->tabOrder, true)) {
            return;
        }

        $this->activeTab = $tab;
    }

    public function getAvailableTranslationsProperty(): array
    {
        $translations = $this->movie->translations ?? [];

        return collect($translations)
            ->map(fn ($value) => array_keys(is_array($value) ? $value : []))
            ->filter()
            ->all();
    }

    public function getCastMembersProperty(): array
    {
        return $this->normaliseCreditEntries($this->movie->cast ?? []);
    }

    public function getCrewMembersProperty(): array
    {
        return $this->normaliseCreditEntries($this->movie->crew ?? []);
    }

    public function getStreamingLinksProperty(): array
    {
        return array_values($this->movie->streaming_links ?? []);
    }

    public function getTrailersProperty(): array
    {
        return array_values($this->movie->trailers ?? []);
    }

    public function render()
    {
        return view('livewire.movie-detail');
    }

    protected function normaliseCreditEntries(array $entries): array
    {
        return collect($entries)
            ->map(function ($entry) {
                if (is_string($entry)) {
                    return ['name' => $entry];
                }

                if (is_array($entry)) {
                    return [
                        'id' => Arr::get($entry, 'id'),
                        'name' => Arr::get($entry, 'name'),
                        'character' => Arr::get($entry, 'character'),
                        'job' => Arr::get($entry, 'job'),
                        'profile_path' => Arr::get($entry, 'profile_path'),
                        'department' => Arr::get($entry, 'department'),
                    ];
                }

                return [];
            })
            ->filter(fn ($entry) => ! empty($entry['name']))
            ->values()
            ->all();
    }
}
