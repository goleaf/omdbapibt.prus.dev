<?php

namespace App\Livewire\Admin\Crud;

use App\Models\Person;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ManagePeople extends CrudComponent
{
    protected function model(): string
    {
        return Person::class;
    }

    protected function view(): string
    {
        return 'livewire.admin.crud.manage-people';
    }

    protected function defaultForm(): array
    {
        return [
            'name' => '',
            'slug' => '',
            'known_for_department' => '',
            'birthday' => '',
            'gender' => '',
            'popularity' => '',
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
                Rule::unique('people', 'slug')->ignore($this->editingId),
            ],
            'form.known_for_department' => ['nullable', 'string', 'max:255'],
            'form.birthday' => ['nullable', 'date'],
            'form.gender' => ['nullable', 'integer', 'between:0,9'],
            'form.popularity' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    protected function query(): Builder
    {
        return Person::query()
            ->when($this->search !== '', function (Builder $query): void {
                $term = '%'.Str::of($this->search)->trim().'%';

                $query->where(function (Builder $inner) use ($term): void {
                    $inner
                        ->where('name', 'like', $term)
                        ->orWhere('slug', 'like', $term)
                        ->orWhere('known_for_department', 'like', $term);
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
            'slug' => $slug->isNotEmpty() ? $slug->value() : Str::slug($name),
            'known_for_department' => Arr::get($data, 'known_for_department') !== '' ? Arr::get($data, 'known_for_department') : null,
            'birthday' => Arr::get($data, 'birthday') !== '' ? Arr::get($data, 'birthday') : null,
            'gender' => Arr::get($data, 'gender') !== '' ? (int) Arr::get($data, 'gender') : null,
            'popularity' => Arr::get($data, 'popularity') !== '' ? (float) Arr::get($data, 'popularity') : null,
        ];
    }

    protected function fillFromModel(Model $model): array
    {
        /** @var Person $model */
        return [
            'name' => $model->name ?? '',
            'slug' => $model->slug ?? '',
            'known_for_department' => $model->known_for_department ?? '',
            'birthday' => optional($model->birthday)->toDateString() ?? '',
            'gender' => $model->gender !== null ? (string) $model->gender : '',
            'popularity' => $model->popularity !== null ? (string) $model->popularity : '',
        ];
    }

    protected function validationAttributeLabels(): array
    {
        return [
            'form.name' => 'name',
            'form.slug' => 'slug',
            'form.known_for_department' => 'department',
            'form.birthday' => 'birthday',
            'form.gender' => 'gender',
            'form.popularity' => 'popularity',
        ];
    }
}
