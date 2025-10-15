<?php

namespace App\Livewire\People;

use App\Models\Movie;
use App\Models\Person;
use App\Models\TvShow;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Component;

class PersonDetail extends Component
{
    public Person $person;

    /** @var array<int, array{locale: string, label: string, text: string}> */
    public array $biographyTranslations = [];

    /** @var array<string, string> */
    public array $personalDetails = [];

    public Collection $movieCreditsByRole;

    public Collection $tvCreditsByRole;

    /**
     * Mount the component with the requested person identifier.
     */
    public function mount(string $person): void
    {
        $this->person = Person::query()
            ->with([
                'movieCredits' => fn ($query) => $query
                    ->select('movies.*')
                    ->withPivot(['role', 'character', 'job', 'department', 'order'])
                    ->orderByDesc('release_date'),
                'tvCredits' => fn ($query) => $query
                    ->select('tv_shows.*')
                    ->withPivot(['role', 'character', 'job', 'department', 'order'])
                    ->orderByDesc('first_air_date'),
            ])
            ->when(is_numeric($person), function ($query) use ($person) {
                $query->where('id', (int) $person)
                    ->orWhere('slug', $person);
            }, function ($query) use ($person) {
                $query->where('slug', $person);
            })
            ->firstOrFail();

        $this->biographyTranslations = $this->prepareBiographyTranslations();
        $this->personalDetails = $this->preparePersonalDetails();
        $this->movieCreditsByRole = $this->groupCreditsByRole($this->person->movieCredits);
        $this->tvCreditsByRole = $this->groupCreditsByRole($this->person->tvCredits);
    }

    /**
     * Prepare the biography translations for display.
     *
     * @return array<int, array{locale: string, label: string, text: string}>
     */
    protected function prepareBiographyTranslations(): array
    {
        $currentLocale = app()->getLocale();

        return collect($this->person->getTranslations('biography'))
            ->filter(fn ($translation) => filled($translation))
            ->sortBy(fn ($translation, $locale) => $locale === $currentLocale ? 0 : 1)
            ->map(function (string $translation, string $locale) use ($currentLocale): array {
                $label = Str::upper($locale);

                if (class_exists(\Locale::class)) {
                    $display = \Locale::getDisplayLanguage($locale, $currentLocale);
                    if ($display) {
                        $label = ucfirst($display) . ' (' . Str::upper($locale) . ')';
                    }
                }

                return [
                    'locale' => $locale,
                    'label' => $label,
                    'text' => $translation,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * Compile the personal details into key/value pairs.
     *
     * @return array<string, string>
     */
    protected function preparePersonalDetails(): array
    {
        $alternateNames = $this->person->alternateNames();

        return collect([
            __('Known For') => $this->person->known_for_department,
            __('Gender') => $this->person->genderLabel(),
            __('Birthday') => $this->person->formattedBirthday(),
            __('Place of Birth') => $this->person->place_of_birth,
            __('Also Known As') => $alternateNames->isNotEmpty() ? $alternateNames->implode(', ') : null,
            __('Deathday') => $this->person->formattedDeathday(),
            __('Popularity') => $this->person->popularity ? number_format($this->person->popularity, 1) : null,
            __('Homepage') => $this->person->homepage,
        ])->filter(fn ($value) => filled($value))
            ->all();
    }

    /**
     * Group a collection of credits by their pivot role.
     */
    protected function groupCreditsByRole(Collection $credits): Collection
    {
        $roleOrder = [
            'cast' => 0,
            'creator' => 1,
            'crew' => 2,
        ];

        return $credits
            ->groupBy(fn ($credit) => Str::of($credit->pivot->role ?? 'other')->lower()->value())
            ->map(function (Collection $group) {
                return $group->sortByDesc(function ($credit) {
                    if ($credit instanceof Movie) {
                        return $credit->release_date?->timestamp ?? 0;
                    }

                    if ($credit instanceof TvShow) {
                        return $credit->first_air_date?->timestamp ?? 0;
                    }

                    return 0;
                })->values();
            })
            ->sortKeysUsing(function (string $a, string $b) use ($roleOrder) {
                $positionA = $roleOrder[$a] ?? PHP_INT_MAX;
                $positionB = $roleOrder[$b] ?? PHP_INT_MAX;

                if ($positionA === $positionB) {
                    return strcmp($a, $b);
                }

                return $positionA <=> $positionB;
            });
    }

    /**
     * Render the component.
     */
    public function render(): View
    {
        return view('livewire.people.person-detail')
            ->layout('layouts.app', [
                'title' => $this->person->name . ' — ' . __('Person'),
            ]);
    }
}
