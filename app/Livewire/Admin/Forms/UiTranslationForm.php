<?php

namespace App\Livewire\Admin\Forms;

use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Form;

class UiTranslationForm extends Form
{
    public string $group = '';

    public string $key = '';

    /**
     * @var array<string, string>
     */
    public array $values = [];

    /**
     * @var string[]
     */
    public array $locales = [];

    public string $fallbackLocale = 'en';

    public ?int $translationId = null;

    /**
     * @param  string[]  $locales
     */
    public function setLocales(array $locales, string $fallbackLocale): void
    {
        $this->locales = array_values(array_unique($locales));
        $this->fallbackLocale = $fallbackLocale;

        $this->values = collect($this->locales)
            ->mapWithKeys(fn (string $locale) => [$locale => trim((string) ($this->values[$locale] ?? ''))])
            ->all();
    }

    public function resetFields(): void
    {
        $this->translationId = null;
        $this->group = '';
        $this->key = '';
        $this->values = collect($this->locales)
            ->mapWithKeys(fn (string $locale) => [$locale => ''])
            ->all();
    }

    public function fillFromArray(string $group, string $key, array $values): void
    {
        $this->group = $group;
        $this->key = $key;
        $this->values = collect($this->locales)
            ->mapWithKeys(function (string $locale) use ($values): array {
                return [$locale => trim((string) ($values[$locale] ?? ''))];
            })
            ->all();
    }

    public function rules(): array
    {
        $normalizedGroup = $this->normalizedGroup();
        $normalizedKey = $this->normalizedKey();

        $rules = [
            'group' => ['required', 'string', 'max:100'],
            'key' => [
                'required',
                'string',
                'max:150',
                Rule::unique('ui_translations', 'key')
                    ->where(fn ($query) => $query->where('group', $normalizedGroup))
                    ->ignore($this->translationId),
            ],
        ];

        foreach ($this->locales as $locale) {
            $localeRules = ['nullable', 'string'];

            if ($locale === $this->fallbackLocale) {
                $localeRules[0] = 'required';
            }

            $rules["values.{$locale}"] = $localeRules;
        }

        return $rules;
    }

    public function messages(): array
    {
        $messages = [
            'group.required' => __('ui.admin.ui_translations.validation.group_required'),
            'key.required' => __('ui.admin.ui_translations.validation.key_required'),
            'key.unique' => __('ui.admin.ui_translations.validation.key_unique'),
        ];

        $messages["values.{$this->fallbackLocale}.required"] = __('ui.admin.ui_translations.validation.value_required', [
            'locale' => strtoupper($this->fallbackLocale),
        ]);

        return $messages;
    }

    public function validationAttributes(): array
    {
        $attributes = [
            'group' => __('ui.admin.ui_translations.form.fields.group'),
            'key' => __('ui.admin.ui_translations.form.fields.key'),
        ];

        foreach ($this->locales as $locale) {
            $attributes["values.{$locale}"] = __('ui.admin.ui_translations.form.fields.value').' ('.strtoupper($locale).')';
        }

        return $attributes;
    }

    /**
     * @return array{group: string, key: string, values: array<string, string>}
     */
    public function payload(): array
    {
        return [
            'group' => $this->normalizedGroup(),
            'key' => $this->normalizedKey(),
            'values' => collect($this->values)
                ->mapWithKeys(function ($value, string $locale): array {
                    $trimmed = trim((string) $value);

                    if ($trimmed === '') {
                        return [];
                    }

                    return [$locale => $trimmed];
                })
                ->all(),
        ];
    }

    public function normalizedGroup(): string
    {
        return Str::slug($this->group, '_');
    }

    public function normalizedKey(): string
    {
        return Str::slug($this->key, '_');
    }
}
