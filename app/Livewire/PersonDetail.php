<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Movie;
use App\Models\Person;
use App\Models\TvShow;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Livewire\Component;

class PersonDetail extends Component
{
    public Person $person;

    /**
     * @var array<string, string>
     */
    public array $personalDetails = [];

    /**
     * @var array<int, array{locale: string, label: string, biography: string}>
     */
    public array $biographyTranslations = [];

    /**
     * @var array<int, array{role_key: string, role_label: string, credits: array<int, array<string, mixed>>}>
     */
    public array $movieCreditsByRole = [];

    /**
     * @var array<int, array{role_key: string, role_label: string, credits: array<int, array<string, mixed>>}>
     */
    public array $tvCreditsByRole = [];

    protected string $locale;

    public function mount(string $identifier): void
    {
        $this->locale = request()->route('locale') ?? app()->getLocale();
        app()->setLocale($this->locale);

        try {
            $this->person = $this->resolvePerson($identifier);
        } catch (ModelNotFoundException) {
            abort(404);
        }

        $this->personalDetails = $this->buildPersonalDetails();
        $this->biographyTranslations = $this->person->formattedBiographyTranslations($this->locale);
        $this->movieCreditsByRole = $this->formatCredits($this->person->movieCredits, 'movie');
        $this->tvCreditsByRole = $this->formatCredits($this->person->tvCredits, 'tv');
    }

    public function render()
    {
        return view('livewire.person-detail');
    }

    private function resolvePerson(string $identifier): Person
    {
        $query = $this->baseQuery();

        $person = (clone $query)
            ->where('slug', $identifier)
            ->first();

        if ($person instanceof Person) {
            return $person;
        }

        if (ctype_digit($identifier)) {
            $person = (clone $query)->whereKey((int) $identifier)->first();
        }

        if ($person instanceof Person) {
            return $person;
        }

        throw new ModelNotFoundException('Person not found.');
    }

    private function baseQuery(): Builder
    {
        return Person::query()
            ->with([
                'movieCredits' => function ($query): void {
                    $query
                        ->select((new Movie)->getTable().'.*')
                        ->withPivot(['role', 'character', 'job', 'department', 'credit_order'])
                        ->orderByDesc('release_date');
                },
                'tvCredits' => function ($query): void {
                    $query
                        ->select((new TvShow)->getTable().'.*')
                        ->withPivot(['role', 'character', 'job', 'department', 'credit_order'])
                        ->orderByDesc('first_air_date');
                },
            ]);
    }

    private function buildPersonalDetails(): array
    {
        $details = [];

        if ($this->person->birthday) {
            $details[__('Born')] = $this->person->birthday->translatedFormat('F j, Y');
            $details[__('Age')] = (string) $this->person->birthday->age;
        }

        if ($this->person->deathday) {
            $details[__('Died')] = $this->person->deathday->translatedFormat('F j, Y');
            unset($details[__('Age')]);
        }

        if ($this->person->place_of_birth) {
            $details[__('Place of birth')] = $this->person->place_of_birth;
        }

        if ($this->person->known_for_department) {
            $details[__('Known for')] = $this->person->known_for_department;
        }

        if ($this->person->popularity) {
            $details[__('Popularity')] = number_format((float) $this->person->popularity, 1);
        }

        return $details;
    }

    private function translate(null|array|string $value): ?string
    {
        if (is_array($value)) {
            if (Arr::has($value, $this->locale) && filled($value[$this->locale])) {
                return (string) $value[$this->locale];
            }

            $fallback = config('app.fallback_locale');

            if ($fallback && Arr::has($value, $fallback) && filled($value[$fallback])) {
                return (string) $value[$fallback];
            }

            return collect($value)
                ->filter(fn (?string $translation): bool => filled($translation))
                ->first();
        }

        return $value;
    }

    private function formatCredits(Collection $credits, string $type): array
    {
        return $credits
            ->map(function (Movie|TvShow $credit) use ($type): array {
                $roleKey = $credit->pivot->role ?? 'other';
                $roleLabel = $this->person->resolveRoleLabel($roleKey);
                $title = $type === 'movie'
                    ? $this->translate($credit->title)
                    : $credit->name;
                $year = $type === 'movie'
                    ? optional($credit->release_date)->format('Y')
                    : optional($credit->first_air_date)->format('Y');

                return [
                    'role_key' => Str::slug($roleLabel) ?: 'other',
                    'role_label' => $roleLabel,
                    'title' => $title ?? ($credit->original_title ?? $credit->name ?? ''),
                    'year' => $year,
                    'character' => $credit->pivot->character,
                    'job' => $credit->pivot->job,
                    'department' => $credit->pivot->department,
                    'poster' => $credit->poster_image_url,
                ];
            })
            ->groupBy('role_label')
            ->map(function (Collection $items): array {
                $first = $items->first();

                return [
                    'role_key' => $first['role_key'],
                    'role_label' => $first['role_label'],
                    'credits' => $items
                        ->sortByDesc('year')
                        ->values()
                        ->all(),
                ];
            })
            ->values()
            ->sortBy('role_label', SORT_NATURAL | SORT_FLAG_CASE)
            ->values()
            ->all();
    }
}
