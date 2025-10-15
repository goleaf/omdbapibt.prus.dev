<?php

namespace App\Livewire;

use App\Services\Movies\RecommendationService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class DashboardRecommendations extends Component
{
    protected RecommendationService $recommendations;

    public int $limit = 6;

    public function boot(RecommendationService $recommendations): void
    {
        $this->recommendations = $recommendations;
    }

    #[Computed]
    public function suggestions(): Collection
    {
        $user = Auth::user();

        if (! $user) {
            return new Collection;
        }

        return $this->recommendations->forUser($user, $this->limit);
    }

    public function render(): View
    {
        return view('livewire.dashboard-recommendations');
    }
}
