<?php

namespace App\Livewire;

use App\Models\Movie;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
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
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function render(): View
    {
        return view('livewire.movie-detail');
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
}
