<?php

namespace App\Livewire\Admin\Crud;

use App\Models\Language;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ManageLanguages extends CrudComponent
{
    protected function model(): string
    {
        return Language::class;
    }

    protected function view(): string
    {
        return 'livewire.admin.crud.manage-languages';
    }

    protected function defaultForm(): array
    {
        return [
            'name' => '',
            'code' => '',
            'native_name' => '',
            'active' => true,
        ];
    }

    protected function formRules(): array
    {
        return [
            'form.name' => ['required', 'string', 'max:255'],
            'form.code' => ['required', 'string', 'max:5', Rule::unique('languages', 'code')->ignore($this->editingId)],
            'form.native_name' => ['nullable', 'string', 'max:255'],
            'form.active' => ['boolean'],
        ];
    }

    protected function query(): Builder
    {
        return Language::query()
            ->when($this->search !== '', function (Builder $query): void {
                $term = '%'.Str::of($this->search)->trim().'%';

                $query->where(function (Builder $inner) use ($term): void {
                    $inner
                        ->where('code', 'like', $term)
                        ->orWhere('name', 'like', $term)
                        ->orWhere('name_translations->en', 'like', $term)
                        ->orWhere('native_name', 'like', $term);
                });
            })
            ->orderBy('name');
    }

    protected function mutateFormData(array $data): array
    {
        $name = Str::of($data['name'] ?? '')->trim();
        $code = Str::of($data['code'] ?? '')->lower()->trim();
        $nativeName = Str::of($data['native_name'] ?? '')->trim();

        return [
            'name_translations' => ['en' => $name->value()],
            'code' => $code->value(),
            'native_name_translations' => $nativeName->isNotEmpty() ? ['en' => $nativeName->value()] : null,
            'active' => (bool) Arr::get($data, 'active', false),
        ];
    }

    protected function fillFromModel(Model $model): array
    {
        /** @var Language $model */
        $translations = $model->name_translations;
        $name = '';

        if (is_array($translations)) {
            $name = (string) ($translations['en'] ?? Arr::first($translations, fn ($value) => is_string($value) && $value !== '', ''));
        }

        $nativeTranslations = $model->native_name_translations;
        $nativeName = '';

        if (is_array($nativeTranslations)) {
            $nativeName = (string) ($nativeTranslations['en'] ?? Arr::first($nativeTranslations, fn ($value) => is_string($value) && $value !== '', ''));
        }

        return [
            'name' => $name ?? '',
            'code' => $model->code ?? '',
            'native_name' => $nativeName ?? '',
            'active' => (bool) $model->active,
        ];
    }

    protected function validationAttributeLabels(): array
    {
        return [
            'form.name' => 'name',
            'form.code' => 'language code',
            'form.native_name' => 'native name',
            'form.active' => 'active flag',
        ];
    }
}
