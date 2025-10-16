<?php

namespace App\Livewire\Admin\Crud;

use App\Models\Movie;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ManageMovies extends CrudComponent
{
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
        ];
    }
}
