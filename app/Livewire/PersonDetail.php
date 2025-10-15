<?php

namespace App\Livewire;

use App\Models\Person;
use App\Support\TmdbImage;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class PersonDetail extends Component
{
    public Person $personModel;

    public array $credits = [];

    public string $biography = '';

    public function mount(string $person): void
    {
        $this->personModel = $this->resolvePerson($person);
        $this->credits = $this->groupCredits();
        $this->biography = $this->resolveBiography();
    }

    public function render(): View
    {
        return view('livewire.person-detail', [
            'profileImage' => TmdbImage::profile($this->personModel->profile_path),
            'posterImage' => TmdbImage::poster($this->personModel->poster_path),
        ]);
    }

    protected function resolvePerson(string $identifier): Person
    {
        $query = Person::query()->with(['movies', 'tvShows']);

        if (is_numeric($identifier)) {
            $person = $query->find((int) $identifier);
        } else {
            $person = $query->where('slug', $identifier)->first();
        }

        abort_if(! $person, 404);

        return $person;
    }

    protected function groupCredits(): array
    {
        $credits = [];

        foreach ($this->personModel->movies as $movie) {
            $pivot = $movie->pivot;
            $type = $pivot->credit_type ?? 'cast';
            $credits[$type]['movies'][] = [
                'title' => $movie->title,
                'role' => $pivot->character ?? $pivot->job,
                'year' => $movie->year,
                'slug' => $movie->slug,
            ];
        }

        foreach ($this->personModel->tvShows as $show) {
            $pivot = $show->pivot;
            $type = $pivot->credit_type ?? 'cast';
            $credits[$type]['shows'][] = [
                'title' => $show->name,
                'role' => $pivot->character ?? $pivot->job,
                'year' => optional($show->first_air_date)->format('Y'),
                'slug' => $show->slug,
            ];
        }

        ksort($credits);

        return $credits;
    }

    protected function resolveBiography(): string
    {
        $translations = $this->personModel->biography_translations ?? [];
        $locale = app()->getLocale();

        if (is_array($translations)) {
            if (! empty($translations[$locale])) {
                return $translations[$locale];
            }

            if (! empty($translations['en'])) {
                return $translations['en'];
            }
        }

        return $this->personModel->biography ?? __('ui.people.no_biography');
    }
}
