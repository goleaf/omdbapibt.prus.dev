<?php

namespace App\Livewire\Admin\Crud;

use App\Models\Country;
use App\Models\Genre;
use App\Models\Language;
use App\Models\Movie;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;

class ManageMovies extends CrudComponent
{
    public array $relationSearch = [
        'genres' => '',
        'languages' => '',
        'countries' => '',
        'tags' => '',
    ];

    protected function model(): string
    {
        return Movie::class;
    }

    protected function view(): string
    {
        return 'livewire.admin.crud.manage-movies';
    }

    protected function defaultForm(): array
    {
        return [
            'title' => '',
            'slug' => '',
            'status' => '',
            'release_date' => '',
            'vote_average' => '',
            'adult' => false,
            'genre_ids' => [],
            'language_ids' => [],
            'country_ids' => [],
            'tag_ids' => [],
        ];
    }

    protected function formRules(): array
    {
        return [
            'form.title' => ['required', 'string', 'max:255'],
            'form.slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('movies', 'slug')->ignore($this->editingId),
            ],
            'form.status' => ['nullable', 'string', 'max:255'],
            'form.release_date' => ['nullable', 'date'],
            'form.vote_average' => ['nullable', 'numeric', 'min:0', 'max:10'],
            'form.adult' => ['boolean'],
            'form.genre_ids' => ['array'],
            'form.genre_ids.*' => ['integer', Rule::exists('genres', 'id')],
            'form.language_ids' => ['array'],
            'form.language_ids.*' => ['integer', Rule::exists('languages', 'id')],
            'form.country_ids' => ['array'],
            'form.country_ids.*' => ['integer', Rule::exists('countries', 'id')],
            'form.tag_ids' => ['array'],
            'form.tag_ids.*' => ['integer', Rule::exists('tags', 'id')],
        ];
    }

    protected function query(): Builder
    {
        return Movie::query()
            ->when($this->search !== '', function (Builder $query): void {
                $term = '%'.Str::of($this->search)->trim().'%';

                $query->where(function (Builder $inner) use ($term): void {
                    $inner
                        ->where('slug', 'like', $term)
                        ->orWhere('title->en', 'like', $term)
                        ->orWhere('original_title', 'like', $term);
                });
            })
            ->orderByDesc('created_at');
    }

    protected function mutateFormData(array $data): array
    {
        $slug = Str::of($data['slug'] ?? '')->trim();
        $title = Str::of($data['title'] ?? '')->trim();

        $payload = [
            'title' => ['en' => $title->value()],
            'slug' => $slug->isNotEmpty() ? $slug->value() : Str::slug($title),
            'status' => Arr::get($data, 'status') !== '' ? Arr::get($data, 'status') : null,
            'release_date' => Arr::get($data, 'release_date') !== '' ? Arr::get($data, 'release_date') : null,
            'vote_average' => Arr::get($data, 'vote_average') !== '' ? (float) Arr::get($data, 'vote_average') : null,
            'adult' => (bool) Arr::get($data, 'adult', false),
        ];

        return $payload;
    }

    protected function fillFromModel(Model $model): array
    {
        /** @var Movie $model */
        $model->loadMissing(['genres:id', 'languages:id', 'countries:id', 'tags:id']);
        $titles = $model->title;

        $title = '';

        if (is_array($titles)) {
            $title = (string) ($titles['en'] ?? Arr::first($titles, fn ($value) => is_string($value) && $value !== '', ''));
        } elseif (is_string($titles)) {
            $title = $titles;
        }

        return [
            'title' => $title,
            'slug' => $model->slug ?? '',
            'status' => $model->status ?? '',
            'release_date' => optional($model->release_date)->toDateString() ?? '',
            'vote_average' => $model->vote_average !== null ? (string) $model->vote_average : '',
            'adult' => (bool) $model->adult,
            'genre_ids' => $model->genres->pluck('id')->map(static fn (int $id) => $id)->all(),
            'language_ids' => $model->languages->pluck('id')->map(static fn (int $id) => $id)->all(),
            'country_ids' => $model->countries->pluck('id')->map(static fn (int $id) => $id)->all(),
            'tag_ids' => $model->tags->pluck('id')->map(static fn (int $id) => $id)->all(),
        ];
    }

    protected function validationAttributeLabels(): array
    {
        return [
            'form.title' => 'title',
            'form.slug' => 'slug',
            'form.status' => 'status',
            'form.release_date' => 'release date',
            'form.vote_average' => 'vote average',
            'form.adult' => 'adult flag',
            'form.genre_ids' => 'genres',
            'form.language_ids' => 'languages',
            'form.country_ids' => 'countries',
            'form.tag_ids' => 'tags',
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
    public function availableTags(): Collection
    {
        $search = $this->relationSearchTerm('tags');
        $selected = $this->sanitizeSelection('tag_ids');

        return Tag::query()
            ->select(['id', 'slug', 'name_i18n', 'type'])
            ->when($search !== '', function (Builder $builder) use ($search): void {
                $like = '%'.$search.'%';

                $builder->where(function (Builder $inner) use ($like): void {
                    $inner
                        ->where('slug', 'like', $like)
                        ->orWhere('name_i18n->en', 'like', $like);
                });
            })
            ->when($selected !== [], function (Builder $builder) use ($selected): void {
                $builder->whereNotIn('id', $selected);
            })
            ->orderBy('slug')
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

    #[Computed]
    public function selectedTags(): Collection
    {
        $ids = $this->sanitizeSelection('tag_ids');

        if ($ids === []) {
            return collect();
        }

        return Tag::query()
            ->select(['id', 'slug', 'name_i18n', 'type'])
            ->whereIn('id', $ids)
            ->get()
            ->sortBy(fn (Tag $tag) => array_search($tag->id, $ids, true))
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

    public function toggleTag(int $tagId): void
    {
        $this->form['tag_ids'] = $this->toggleIds($this->sanitizeSelection('tag_ids'), $tagId);
    }

    public function delete(int $id): void
    {
        /** @var Movie $model */
        $model = $this->findModel($id);

        $this->authorize('delete', $model);

        $model->genres()->detach();
        $model->languages()->detach();
        $model->countries()->detach();
        $model->tags()->detach();

        $model->delete();

        if ($this->editingId === $id) {
            $this->create();
        }

        $this->dispatch('record-deleted');
    }

    protected function afterSave(Model $model): void
    {
        /** @var Movie $model */
        $model->genres()->sync($this->sanitizeSelection('genre_ids'));
        $model->languages()->sync($this->sanitizeSelection('language_ids'));
        $model->countries()->sync($this->sanitizeSelection('country_ids'));
        $model->tags()->sync(
            collect($this->sanitizeSelection('tag_ids'))
                ->values()
                ->mapWithKeys(fn (int $tagId, int $index) => [
                    $tagId => [
                        'user_id' => null,
                        'weight' => ($index + 1) * 10,
                    ],
                ])
                ->all()
        );
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
