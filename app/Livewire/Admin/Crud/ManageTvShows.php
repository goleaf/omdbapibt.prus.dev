<?php

namespace App\Livewire\Admin\Crud;

use App\Models\Country;
use App\Models\Genre;
use App\Models\Language;
use App\Models\TvShow;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;

class ManageTvShows extends CrudComponent
{
    public array $relationSearch = [
        'genres' => '',
        'languages' => '',
        'countries' => '',
    ];

    protected function model(): string
    {
        return TvShow::class;
    }

    protected function view(): string
    {
        return 'livewire.admin.crud.manage-tv-shows';
    }

    protected function defaultForm(): array
    {
        return [
            'name' => '',
            'slug' => '',
            'status' => '',
            'first_air_date' => '',
            'vote_average' => '',
            'adult' => false,
            'genre_ids' => [],
            'language_ids' => [],
            'country_ids' => [],
        ];
    }

    protected function formRules(): array
    {
        return [
            'form.name' => ['required', 'string', 'max:255'],
            'form.slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('tv_shows', 'slug')->ignore($this->editingId),
            ],
            'form.status' => ['nullable', 'string', 'max:255'],
            'form.first_air_date' => ['nullable', 'date'],
            'form.vote_average' => ['nullable', 'numeric', 'min:0', 'max:10'],
            'form.adult' => ['boolean'],
            'form.genre_ids' => ['array'],
            'form.genre_ids.*' => ['integer', Rule::exists('genres', 'id')],
            'form.language_ids' => ['array'],
            'form.language_ids.*' => ['integer', Rule::exists('languages', 'id')],
            'form.country_ids' => ['array'],
            'form.country_ids.*' => ['integer', Rule::exists('countries', 'id')],
        ];
    }

    protected function query(): Builder
    {
        return TvShow::query()
            ->when($this->search !== '', function (Builder $query): void {
                $term = '%'.Str::of($this->search)->trim().'%';

                $query->where(function (Builder $inner) use ($term): void {
                    $inner
                        ->where('slug', 'like', $term)
                        ->orWhere('name', 'like', $term)
                        ->orWhere('original_name', 'like', $term)
                        ->orWhere('name_translations->en', 'like', $term);
                });
            })
            ->orderByDesc('created_at');
    }

    protected function mutateFormData(array $data): array
    {
        $slug = Str::of($data['slug'] ?? '')->trim();
        $name = Str::of($data['name'] ?? '')->trim();

        return [
            'name' => $name->value(),
            'name_translations' => ['en' => $name->value()],
            'slug' => $slug->isNotEmpty() ? $slug->value() : Str::slug($name),
            'status' => Arr::get($data, 'status') !== '' ? Arr::get($data, 'status') : null,
            'first_air_date' => Arr::get($data, 'first_air_date') !== '' ? Arr::get($data, 'first_air_date') : null,
            'vote_average' => Arr::get($data, 'vote_average') !== '' ? (float) Arr::get($data, 'vote_average') : null,
            'adult' => (bool) Arr::get($data, 'adult', false),
        ];
    }

    protected function fillFromModel(Model $model): array
    {
        /** @var TvShow $model */
        $model->loadMissing(['genres:id', 'languages:id', 'countries:id']);
        $name = $model->name;

        if (! is_string($name) || $name === '') {
            $translations = $model->name_translations;
            $name = '';

            if (is_array($translations)) {
                $name = (string) ($translations['en'] ?? Arr::first($translations, fn ($value) => is_string($value) && $value !== '', ''));
            }
        }

        return [
            'name' => $name ?? '',
            'slug' => $model->slug ?? '',
            'status' => $model->status ?? '',
            'first_air_date' => optional($model->first_air_date)->toDateString() ?? '',
            'vote_average' => $model->vote_average !== null ? (string) $model->vote_average : '',
            'adult' => (bool) $model->adult,
            'genre_ids' => $model->genres->pluck('id')->map(static fn (int $id) => $id)->all(),
            'language_ids' => $model->languages->pluck('id')->map(static fn (int $id) => $id)->all(),
            'country_ids' => $model->countries->pluck('id')->map(static fn (int $id) => $id)->all(),
        ];
    }

    protected function validationAttributeLabels(): array
    {
        return [
            'form.name' => 'name',
            'form.slug' => 'slug',
            'form.status' => 'status',
            'form.first_air_date' => 'first air date',
            'form.vote_average' => 'vote average',
            'form.adult' => 'adult flag',
            'form.genre_ids' => 'genres',
            'form.language_ids' => 'languages',
            'form.country_ids' => 'countries',
        ];
    }

    #[Computed]
    public function availableGenres(): Collection
    {
        $search = $this->relationSearchTerm('genres');
        $selected = $this->sanitizeSelection('genre_ids');

        return Genre::query()
            ->select(['id', 'name', 'name_translations', 'slug'])
            ->when($search !== '', function (Builder $builder) use ($search): void {
                $like = '%'.$search.'%';

                $builder->where(function (Builder $inner) use ($like): void {
                    $inner
                        ->where('slug', 'like', $like)
                        ->orWhere('name', 'like', $like)
                        ->orWhere('name_translations->en', 'like', $like);
                });
            })
            ->when($selected !== [], function (Builder $builder) use ($selected): void {
                $builder->whereNotIn('id', $selected);
            })
            ->orderBy('name')
            ->limit(12)
            ->get();
    }

    #[Computed]
    public function availableLanguages(): Collection
    {
        $search = $this->relationSearchTerm('languages');
        $selected = $this->sanitizeSelection('language_ids');

        return Language::query()
            ->select(['id', 'code', 'name_translations', 'native_name_translations'])
            ->when($search !== '', function (Builder $builder) use ($search): void {
                $like = '%'.$search.'%';

                $builder->where(function (Builder $inner) use ($like): void {
                    $inner
                        ->where('code', 'like', $like)
                        ->orWhere('name_translations->en', 'like', $like)
                        ->orWhere('native_name_translations->en', 'like', $like);
                });
            })
            ->when($selected !== [], function (Builder $builder) use ($selected): void {
                $builder->whereNotIn('id', $selected);
            })
            ->orderBy('code')
            ->limit(12)
            ->get();
    }

    #[Computed]
    public function availableCountries(): Collection
    {
        $search = $this->relationSearchTerm('countries');
        $selected = $this->sanitizeSelection('country_ids');

        return Country::query()
            ->select(['id', 'code', 'name', 'name_translations'])
            ->when($search !== '', function (Builder $builder) use ($search): void {
                $like = '%'.$search.'%';

                $builder->where(function (Builder $inner) use ($like): void {
                    $inner
                        ->where('code', 'like', $like)
                        ->orWhere('name', 'like', $like)
                        ->orWhere('name_translations->en', 'like', $like);
                });
            })
            ->when($selected !== [], function (Builder $builder) use ($selected): void {
                $builder->whereNotIn('id', $selected);
            })
            ->orderBy('name')
            ->limit(12)
            ->get();
    }

    #[Computed]
    public function selectedGenres(): Collection
    {
        $ids = $this->sanitizeSelection('genre_ids');

        if ($ids === []) {
            return collect();
        }

        return Genre::query()
            ->select(['id', 'name', 'name_translations', 'slug'])
            ->whereIn('id', $ids)
            ->get()
            ->sortBy(fn (Genre $genre) => array_search($genre->id, $ids, true))
            ->values();
    }

    #[Computed]
    public function selectedLanguages(): Collection
    {
        $ids = $this->sanitizeSelection('language_ids');

        if ($ids === []) {
            return collect();
        }

        return Language::query()
            ->select(['id', 'code', 'name_translations', 'native_name_translations'])
            ->whereIn('id', $ids)
            ->get()
            ->sortBy(fn (Language $language) => array_search($language->id, $ids, true))
            ->values();
    }

    #[Computed]
    public function selectedCountries(): Collection
    {
        $ids = $this->sanitizeSelection('country_ids');

        if ($ids === []) {
            return collect();
        }

        return Country::query()
            ->select(['id', 'code', 'name', 'name_translations'])
            ->whereIn('id', $ids)
            ->get()
            ->sortBy(fn (Country $country) => array_search($country->id, $ids, true))
            ->values();
    }

    public function toggleGenre(int $genreId): void
    {
        $this->form['genre_ids'] = $this->toggleIds($this->sanitizeSelection('genre_ids'), $genreId);
    }

    public function toggleLanguage(int $languageId): void
    {
        $this->form['language_ids'] = $this->toggleIds($this->sanitizeSelection('language_ids'), $languageId);
    }

    public function toggleCountry(int $countryId): void
    {
        $this->form['country_ids'] = $this->toggleIds($this->sanitizeSelection('country_ids'), $countryId);
    }

    public function delete(int $id): void
    {
        /** @var TvShow $model */
        $model = $this->findModel($id);

        $this->authorize('delete', $model);

        $model->genres()->detach();
        $model->languages()->detach();
        $model->countries()->detach();

        $model->delete();

        if ($this->editingId === $id) {
            $this->create();
        }

        $this->dispatch('record-deleted');
    }

    protected function afterSave(Model $model): void
    {
        /** @var TvShow $model */
        $model->genres()->sync($this->sanitizeSelection('genre_ids'));
        $model->languages()->sync($this->sanitizeSelection('language_ids'));
        $model->countries()->sync($this->sanitizeSelection('country_ids'));
    }

    private function relationSearchTerm(string $key): string
    {
        return trim((string) ($this->relationSearch[$key] ?? ''));
    }

    private function sanitizeSelection(string $key): array
    {
        $value = $this->form[$key] ?? [];

        if (! is_array($value)) {
            return [];
        }

        $ids = array_map(static fn ($id) => (int) $id, $value);
        $ids = array_filter($ids, static fn (int $id) => $id > 0);

        return array_values(array_unique($ids));
    }

    /**
     * @param  array<int>  $ids
     */
    private function toggleIds(array $ids, int $id): array
    {
        $id = (int) $id;

        if (in_array($id, $ids, true)) {
            return array_values(array_filter($ids, static fn (int $value) => $value !== $id));
        }

        $ids[] = $id;

        return array_values(array_unique($ids));
    }
}
