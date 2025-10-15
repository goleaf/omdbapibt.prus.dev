<?php

namespace App\Livewire;

use App\Services\RecommendationService;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Livewire\Component;

class Recommendations extends Component
{
    /**
     * @var array<int, array<string, mixed>>
     */
    public array $recommendations = [];

    public bool $hasWatchHistory = false;

    public string $lastUpdated = '';

    public function mount(RecommendationService $service): void
    {
        $user = auth()->user();

        if ($user === null) {
            return;
        }

        $payload = $service->getRecommendationsFor($user);

        $this->recommendations = Arr::get($payload, 'items', []);
        $this->hasWatchHistory = ! empty(Arr::get($payload, 'profile.watched_ids', []));
        $this->lastUpdated = $this->formatUpdatedTimestamp(Arr::get($payload, 'generated_at'));
    }

    public function refresh(RecommendationService $service): void
    {
        $user = auth()->user();

        if ($user === null) {
            return;
        }

        $payload = $service->refreshRecommendations($user);

        $this->recommendations = Arr::get($payload, 'items', []);
        $this->hasWatchHistory = ! empty(Arr::get($payload, 'profile.watched_ids', []));
        $this->lastUpdated = $this->formatUpdatedTimestamp(Arr::get($payload, 'generated_at'));
    }

    public function render()
    {
        return view('livewire.recommendations');
    }

    private function formatUpdatedTimestamp(?string $timestamp): string
    {
        if (empty($timestamp)) {
            return '';
        }

        return Carbon::parse($timestamp)->diffForHumans();
    }
}
