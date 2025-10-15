<?php

namespace App\Livewire\Admin;

use App\Models\UiTranslation;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class UiTranslationsManager extends Component
{
    public array $form = [];

    public array $locales = [];

    public function mount(): void
    {
        if (! Auth::check() || ! Auth::user()->isAdmin()) {
            abort(403);
        }

        $this->locales = config('translatable.locales', [config('app.fallback_locale', 'en')]);

        $this->resetForm();
    }

    public function render(): View
    {
        return view('livewire.admin.ui-translation-manager', [
            'translations' => UiTranslation::query()
                ->orderBy('group')
                ->orderBy('key')
                ->get(),
        ])->layout('layouts.app', [
            'title' => 'UI Translations',
            'header' => 'UI Translations',
            'subheader' => 'Manage localized copy across supported locales.',
        ]);
    }

    public function startCreating(): void
    {
        $this->resetForm();
    }

    public function startEditing(int $translationId): void
    {
        $translation = UiTranslation::query()->findOrFail($translationId);

        $this->form['id'] = $translation->id;
        $this->form['group'] = $translation->group;
        $this->form['key'] = $translation->key;

        foreach ($this->locales as $locale) {
            $this->form['translations'][$locale] = (string) $translation->getTranslation('value', $locale, false);
        }
    }

    public function save(): void
    {
        $this->validate($this->rules());

        if ($this->form['id']) {
            $translation = UiTranslation::query()->findOrFail($this->form['id']);
            $translation->fill([
                'group' => $this->form['group'],
                'key' => $this->form['key'],
            ]);
        } else {
            $translation = new UiTranslation([
                'group' => $this->form['group'],
                'key' => $this->form['key'],
            ]);
        }

        foreach ($this->locales as $locale) {
            $translation->setTranslation('value', $locale, $this->form['translations'][$locale] ?? '');
        }

        $translation->save();

        session()->flash('status', 'Translation saved successfully.');

        $this->resetForm();
    }

    public function deleteTranslation(int $translationId): void
    {
        $translation = UiTranslation::query()->findOrFail($translationId);
        $translation->delete();

        if ($this->form['id'] === $translationId) {
            $this->resetForm();
        }

        session()->flash('status', 'Translation deleted.');
    }

    protected function resetForm(): void
    {
        $this->form = [
            'id' => null,
            'group' => '',
            'key' => '',
            'translations' => array_fill_keys($this->locales, ''),
        ];

        $this->resetValidation();
    }

    protected function rules(): array
    {
        $uniqueRule = Rule::unique('ui_translations', 'key')
            ->where('group', $this->form['group']);

        if ($this->form['id']) {
            $uniqueRule->ignore($this->form['id']);
        }

        $translationRules = [];

        foreach ($this->locales as $locale) {
            $translationRules["form.translations.{$locale}"] = ['required', 'string'];
        }

        return array_merge([
            'form.group' => ['required', 'string', 'max:150'],
            'form.key' => ['required', 'string', 'max:150', $uniqueRule],
        ], $translationRules);
    }
}
