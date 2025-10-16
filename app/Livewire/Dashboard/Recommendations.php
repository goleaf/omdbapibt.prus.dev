<?php

namespace App\Livewire\Dashboard;

use App\Models\Movie;
use App\Models\User;
use App\Services\Movies\RecommendationService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Recommendations extends Component
{
    public array $items = [];

    public bool $isHydrated = false;

    protected RecommendationService $recommendationService;

    public function boot(RecommendationService $recommendationService): void
    {
        $this->recommendationService = $recommendationService;
    }

    public function mount(): void
    {
        $this->loadRecommendations();
    }

    public function refreshRecommendations(): void
    {
        $user = $this->currentUser();

        if (! $user) {
            return;
        }

        $this->recommendationService->flush($user);
        $this->loadRecommendations();
        $this->dispatch('recommendations-refreshed');
    }

    #[Computed]
    public function hasRecommendations(): bool
    {
        return $this->isHydrated && $this->items !== [];
    }

    public function render(): View
    {
        return view('livewire.dashboard.recommendations');
    }

    protected function loadRecommendations(): void
    {
        $user = $this->currentUser();

        if (! $user) {
            $this->items = [];
            $this->isHydrated = true;

            return;
        }

        $recommendations = $this->recommendationService
            ->recommendFor($user)
            ->map(fn (Movie $movie) => [
                'id' => $movie->getKey(),
                'title' => $movie->localizedTitle(),
                'tagline' => $movie->tagline,
                'poster_path' => $movie->poster_path,
                'vote_average' => $movie->vote_average,
                'popularity' => $movie->popularity,
                'genres' => $movie->genres->map(fn ($genre) => $genre->localizedName())->filter()->take(3)->implode(', '),
                'slug' => $movie->slug,
            ]);

        $this->items = $recommendations->all();
        $this->isHydrated = true;
    }

    protected function currentUser(): ?User
    {
        /** @var User|null $user */
        $user = auth()->user();

        return $user;
    }
}
