<?php

namespace App\Livewire\Admin\Crud;

use App\Models\TvShow;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ManageTvShows extends CrudComponent
{
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
        ];
    }
}
