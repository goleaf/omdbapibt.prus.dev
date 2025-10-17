<?php

namespace App\Livewire;

use App\Models\Movie;
use App\Models\Rating;
use App\Services\Movies\RatingService;
use App\Support\TmdbImage;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Livewire\Component;

class MovieDetail extends Component
{
    public Movie $movieModel;

    public string $activeTab = 'overview';

    public array $translations = [];

    public array $streaming = [];

    public array $trailers = [];

    public array $cast = [];

    public array $crew = [];

    public ?int $userRating = null;

    public bool $userLiked = false;

    public bool $userDisliked = false;

    public function mount(string $movie): void
    {
        $this->movieModel = $this->resolveMovie($movie);
        Gate::authorize('view', $this->movieModel);
        $this->translations = $this->movieModel->translation_metadata ?? [];
        $this->streaming = $this->movieModel->streaming_links ?? [];
        $this->trailers = $this->movieModel->trailers ?? [];
        $this->cast = $this->movieModel->people
            ->where('pivot.credit_type', 'cast')
            ->take(10)
            ->map(fn ($person) => [
                'name' => $person->name,
                'role' => $person->pivot->character ?? null,
            ])->values()->all();

        $this->crew = $this->movieModel->people
            ->where('pivot.credit_type', 'crew')
            ->take(10)
            ->map(fn ($person) => [
                'name' => $person->name,
                'role' => $person->pivot->job ?? $person->pivot->department,
            ])->values()->all();

        if ($user = Auth::user()) {
            $this->applyRating(
                app(RatingService::class)->findForUser($user, $this->movieModel)
            );
        }
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function submitRating(int $score, RatingService $ratingService): void
    {
        $user = Auth::user();

        if (! $user) {
            abort(403, 'Authentication required.');
        }

        $rating = $ratingService->submitScore($user, $this->movieModel, $score);

        $this->applyRating($rating);
    }

    public function toggleLike(RatingService $ratingService): void
    {
        $user = Auth::user();

        if (! $user) {
            abort(403, 'Authentication required.');
        }

        $rating = $ratingService->toggleLike($user, $this->movieModel);

        $this->applyRating($rating);
    }

    public function toggleDislike(RatingService $ratingService): void
    {
        $user = Auth::user();

        if (! $user) {
            abort(403, 'Authentication required.');
        }

        $rating = $ratingService->toggleDislike($user, $this->movieModel);

        $this->applyRating($rating);
    }

    public function render(): View
    {
        $tabs = collect(['overview', 'credits', 'streaming', 'trailers', 'translations', 'reviews'])
            ->mapWithKeys(function (string $tab): array {
                $label = __('ui.movies.tabs.'.$tab);

                if ($label === 'ui.movies.tabs.'.$tab) {
                    $label = Str::headline($tab);
                }

                return [$tab => $label];
            })
            ->all();

        return view('livewire.movie-detail', [
            'posterUrl' => TmdbImage::poster($this->movieModel->poster_path),
            'tabs' => $tabs,
        ]);
    }

    protected function resolveMovie(string $value): Movie
    {
        $query = Movie::query()->with(['genres', 'people']);

        if (is_numeric($value)) {
            $movie = $query->find((int) $value);
        } else {
            $movie = $query->where('slug', $value)->first();
        }

        abort_if(! $movie, 404);

        return $movie;
    }

    protected function applyRating(?Rating $rating): void
    {
        $this->userRating = $rating?->rating;
        $this->userLiked = (bool) ($rating?->liked);
        $this->userDisliked = (bool) ($rating?->disliked);
    }
}
