<?php

namespace App\Livewire\Admin;

use App\Models\UiTranslation;
use App\Support\UiTranslationRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;

class UiTranslationManager extends Component
{
    use AuthorizesRequests;

    public array $locales = [];

    public array $form = [];

    public ?int $editingId = null;

    public ?int $pendingDeletionId = null;

    public string $statusMessage = '';

    public string $fallbackLocale;

    public function mount(): void
    {
        $this->authorize('viewAny', UiTranslation::class);
        $this->authorize('create', UiTranslation::class);

        $configuredLocales = (array) config('translatable.locales', [config('app.locale')]);
        $fallback = (string) config('translatable.fallback_locale', config('app.fallback_locale', 'en'));

        if (! in_array($fallback, $configuredLocales, true)) {
            $configuredLocales[] = $fallback;
        }

        $this->locales = array_values(array_unique($configuredLocales));
        $this->fallbackLocale = $fallback;

        $this->resetForm();
    }

    public function render(): View
    {
        $translations = UiTranslation::query()
            ->ordered()
            ->get();

        return view('livewire.admin.ui-translation-manager', [
            'translations' => $translations,
        ]);
    }

    public function startCreate(): void
    {
        $this->authorize('create', UiTranslation::class);

        $this->editingId = null;
        $this->pendingDeletionId = null;
        $this->statusMessage = '';
        $this->resetForm();
    }

    public function edit(int $translationId): void
    {
        $translation = UiTranslation::query()->findOrFail($translationId);

        $this->authorize('update', $translation);

        $this->editingId = $translation->id;
        $this->pendingDeletionId = null;
        $this->statusMessage = '';

        $this->resetForm(
            values: $translation->getTranslations('value'),
            group: $translation->group,
            key: $translation->key,
        );
    }

    public function confirmDeletion(int $translationId): void
    {
        $translation = UiTranslation::query()->findOrFail($translationId);

        $this->authorize('delete', $translation);

        $this->pendingDeletionId = $translation->id;
        $this->statusMessage = '';
    }

    public function cancelDeletion(): void
    {
        $this->authorize('delete', UiTranslation::class);

        $this->pendingDeletionId = null;
    }

    public function deleteConfirmed(): void
    {
        $this->authorize('delete', UiTranslation::class);

        if (! $this->pendingDeletionId) {
            return;
        }

        $translation = UiTranslation::query()->find($this->pendingDeletionId);

        if ($translation) {
            $this->authorize('delete', $translation);
            $translation->delete();
            $this->repository()->refreshAndRegister();
            $this->statusMessage = __('Translation deleted.');
        }

        if ($this->editingId === $this->pendingDeletionId) {
            $this->startCreate();
        }

        $this->pendingDeletionId = null;
    }

    public function refreshCache(): void
    {
        $this->authorize('refreshCache', UiTranslation::class);

        $this->repository()->refreshAndRegister();
        $this->statusMessage = __('Translation cache refreshed.');
    }

    public function save(): void
    {
        $translation = null;

        if ($this->editingId) {
            $translation = UiTranslation::query()->find($this->editingId);

            if ($translation) {
                $this->authorize('update', $translation);
            } else {
                $this->authorize('create', UiTranslation::class);
            }
        } else {
            $this->authorize('create', UiTranslation::class);
        }

        $this->statusMessage = '';

        $rules = [
            'form.group' => ['required', 'string', 'max:100'],
            'form.key' => [
                'required',
                'string',
                'max:150',
                Rule::unique('ui_translations', 'key')
                    ->where(fn ($query) => $query->where('group', $this->form['group'] ?? ''))
                    ->ignore($this->editingId),
            ],
        ];

        foreach ($this->locales as $locale) {
            $rules["form.values.{$locale}"] = ['nullable', 'string'];
        }

        $rules["form.values.{$this->fallbackLocale}"][] = 'required';

        $this->validate($rules);

        $payload = $this->cleanPayload();

        if (! $translation) {
            $translation = new UiTranslation;
        }

        $translation->group = Str::slug($this->form['group'], '_');
        $translation->key = Str::slug($this->form['key'], '_');
        $translation->setTranslations('value', $payload);
        $translation->save();

        $this->repository()->refreshAndRegister();

        $this->statusMessage = $this->editingId ? __('Translation updated.') : __('Translation saved.');

        $this->editingId = $translation->id;
        $this->resetForm(
            values: $translation->getTranslations('value'),
            group: $translation->group,
            key: $translation->key,
        );
    }

    protected function resetForm(?array $values = null, ?string $group = null, ?string $key = null): void
    {
        $defaults = [];

        foreach ($this->locales as $locale) {
            $defaults[$locale] = trim((string) ($values[$locale] ?? ''));
        }

        $this->form = [
            'group' => $group ?? '',
            'key' => $key ?? '',
            'values' => $defaults,
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function cleanPayload(): array
    {
        $values = [];

        foreach ($this->locales as $locale) {
            $value = trim((string) ($this->form['values'][$locale] ?? ''));

            if ($value !== '') {
                $values[$locale] = $value;
            }
        }

        return $values;
    }

    protected function repository(): UiTranslationRepository
    {
        return app(UiTranslationRepository::class);
    }
}
