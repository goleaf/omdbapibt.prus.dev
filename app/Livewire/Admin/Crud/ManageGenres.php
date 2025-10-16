<?php

namespace App\Livewire\Admin\Crud;

use App\Models\Genre;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ManageGenres extends CrudComponent
{
    protected function model(): string
    {
        return Genre::class;
    }

    protected function view(): string
    {
        return 'livewire.admin.crud.manage-genres';
    }

    protected function defaultForm(): array
    {
        return [
            'name' => '',
            'slug' => '',
            'tmdb_id' => '',
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
                Rule::unique('genres', 'slug')->ignore($this->editingId),
            ],
            'form.tmdb_id' => ['nullable', 'integer', 'min:1', Rule::unique('genres', 'tmdb_id')->ignore($this->editingId)],
        ];
    }

    protected function query(): Builder
    {
        return Genre::query()
            ->when($this->search !== '', function (Builder $query): void {
                $term = '%'.Str::of($this->search)->trim().'%';

                $query->where(function (Builder $inner) use ($term): void {
                    $inner
                        ->where('slug', 'like', $term)
                        ->orWhere('name', 'like', $term)
                        ->orWhere('name_translations->en', 'like', $term);
                });
            })
            ->orderBy('name');
    }

    protected function mutateFormData(array $data): array
    {
        $slug = Str::of($data['slug'] ?? '')->slug('-');
        $name = Str::of($data['name'] ?? '')->trim();

        return [
            'name' => $name->value(),
            'name_translations' => ['en' => $name->value()],
            'slug' => $slug->isNotEmpty() ? $slug->value() : Str::slug($name),
            'tmdb_id' => Arr::get($data, 'tmdb_id') !== '' ? (int) Arr::get($data, 'tmdb_id') : null,
        ];
    }

    protected function fillFromModel(Model $model): array
    {
        /** @var Genre $model */
        $name = $model->getRawOriginal('name');

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
            'tmdb_id' => $model->tmdb_id !== null ? (string) $model->tmdb_id : '',
        ];
    }

    protected function validationAttributeLabels(): array
    {
        return [
            'form.name' => 'name',
            'form.slug' => 'slug',
            'form.tmdb_id' => 'TMDb ID',
        ];
    }
}
