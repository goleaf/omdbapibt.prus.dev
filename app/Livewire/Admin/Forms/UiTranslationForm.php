<?php

namespace App\Livewire\Admin\Forms;

use App\Models\UiTranslation;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
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
     * @var array<int, string>
     */
    public array $locales = [];

    public string $fallbackLocale = 'en';

    protected ?int $translationId = null;

    /**
     * @param  array<int, string>  $locales
     */
    public function configure(array $locales, string $fallbackLocale): void
    {
        $this->locales = array_values($locales);
        $this->fallbackLocale = $fallbackLocale;
        $this->values = $this->defaultValues($this->values);
    }

    public function startCreating(): void
    {
        $this->translationId = null;
        $this->group = '';
        $this->key = '';
        $this->values = $this->defaultValues();
    }

    public function fillFromModel(UiTranslation $translation): void
    {
        $this->translationId = $translation->id;
        $this->group = $translation->group;
        $this->key = $translation->key;
        $this->values = $this->defaultValues($translation->getTranslations('value'));
    }

    public function setTranslationId(?int $translationId): void
    {
        $this->translationId = $translationId;
    }

    public function rules(): array
    {
        $rules = [
            'group' => ['required', 'string', 'max:100'],
            'key' => ['required', 'string', 'max:150'],
        ];

        foreach ($this->locales as $locale) {
            $rules["values.{$locale}"] = $this->localeRules($locale);
        }

        return $rules;
    }

    public function messages(): array
    {
        $messages = [];

        foreach ($this->locales as $locale) {
            if ($locale === $this->fallbackLocale) {
                $messages["values.{$locale}.required"] = trans('admin.ui_translations.validation.value_required', [
                    'locale' => $this->localeLabel($locale),
                ]);
            }
        }

        return $messages;
    }

    public function attributes(): array
    {
        $attributes = [
            'group' => trans('admin.ui_translations.fields.group'),
            'key' => trans('admin.ui_translations.fields.key'),
        ];

        foreach ($this->locales as $locale) {
            $attributes["values.{$locale}"] = trans('admin.ui_translations.fields.value', [
                'locale' => $this->localeLabel($locale),
            ]);
        }

        return $attributes;
    }

    public function persist(?UiTranslation $translation = null): UiTranslation
    {
        $this->validate($this->rules(), $this->messages(), $this->attributes());
        $this->assertUniqueCombination();

        $translation ??= new UiTranslation;

        $translation->group = $this->normalizedGroup();
        $translation->key = $this->normalizedKey();
        $translation->setTranslations('value', $this->cleanValues());
        $translation->save();

        $this->translationId = $translation->id;
        $this->fillFromModel($translation);

        return $translation;
    }

    protected function localeRules(string $locale): array
    {
        $rules = ['nullable', 'string'];

        if ($locale === $this->fallbackLocale) {
            $rules[] = 'required';
        }

        return $rules;
    }

    /**
     * @param  array<string, string>|null  $values
     * @return array<string, string>
     */
    protected function defaultValues(?array $values = null): array
    {
        $values = $values ?? [];
        $defaults = [];

        foreach ($this->locales as $locale) {
            $defaults[$locale] = trim((string) ($values[$locale] ?? ''));
        }

        return $defaults;
    }

    /**
     * @return array<string, string>
     */
    protected function cleanValues(): array
    {
        $values = [];

        foreach ($this->locales as $locale) {
            $value = trim((string) ($this->values[$locale] ?? ''));

            if ($value !== '') {
                $values[$locale] = $value;
            }
        }

        return $values;
    }

    protected function normalizedGroup(): string
    {
        return Str::slug($this->group, '_');
    }

    protected function normalizedKey(): string
    {
        return Str::slug($this->key, '_');
    }

    protected function localeLabel(string $locale): string
    {
        $key = "admin.ui_translations.locales.{$locale}";
        $label = trans($key);

        if ($label === $key) {
            return Str::upper($locale);
        }

        return $label;
    }

    protected function assertUniqueCombination(): void
    {
        $exists = UiTranslation::query()
            ->where('group', $this->normalizedGroup())
            ->where('key', $this->normalizedKey())
            ->when($this->translationId, function ($query) {
                $query->where('id', '!=', $this->translationId);
            })
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'form.key' => [
                    trans('admin.ui_translations.validation.key_unique', [
                        'group' => $this->normalizedGroup(),
                        'key' => $this->normalizedKey(),
                    ]),
                ],
            ]);
        }
    }
}
