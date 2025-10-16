<?php

namespace App\Livewire\Admin\Crud;

use App\Models\Country;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ManageCountries extends CrudComponent
{
    protected function model(): string
    {
        return Country::class;
    }

    protected function view(): string
    {
        return 'livewire.admin.crud.manage-countries';
    }

    protected function defaultForm(): array
    {
        return [
            'name' => '',
            'code' => '',
            'active' => true,
        ];
    }

    protected function formRules(): array
    {
        return [
            'form.name' => ['required', 'string', 'max:255'],
            'form.code' => ['required', 'string', 'size:2', Rule::unique('countries', 'code')->ignore($this->editingId)],
            'form.active' => ['boolean'],
        ];
    }

    protected function query(): Builder
    {
        return Country::query()
            ->when($this->search !== '', function (Builder $query): void {
                $term = '%'.Str::of($this->search)->trim().'%';

                $query->where(function (Builder $inner) use ($term): void {
                    $inner
                        ->where('code', 'like', $term)
                        ->orWhere('name', 'like', $term)
                        ->orWhere('name_translations->en', 'like', $term);
                });
            })
            ->orderBy('name');
    }

    protected function mutateFormData(array $data): array
    {
        $name = Str::of($data['name'] ?? '')->trim();
        $code = Str::of($data['code'] ?? '')->upper()->trim();

        return [
            'name' => $name->value(),
            'name_translations' => ['en' => $name->value()],
            'code' => $code->value(),
            'active' => (bool) Arr::get($data, 'active', false),
        ];
    }

    protected function fillFromModel(Model $model): array
    {
        /** @var Country $model */
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
            'code' => $model->code ?? '',
            'active' => (bool) $model->active,
        ];
    }

    protected function validationAttributeLabels(): array
    {
        return [
            'form.name' => 'name',
            'form.code' => 'country code',
            'form.active' => 'active flag',
        ];
    }
}
