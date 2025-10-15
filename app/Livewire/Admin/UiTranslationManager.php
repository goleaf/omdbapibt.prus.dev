<?php

namespace App\Livewire\Admin;

use App\Livewire\Admin\Forms\UiTranslationForm;
use App\Models\UiTranslation;
use App\Support\UiTranslationRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class UiTranslationManager extends Component
{
    use AuthorizesRequests;

    public array $locales = [];

    public UiTranslationForm $form;

    public ?int $editingId = null;

    public ?int $pendingDeletionId = null;

    public string $statusMessage = '';

    public string $fallbackLocale;

    public function mount(): void
    {
        $this->authorize('view', UiTranslation::class);

        $configuredLocales = (array) config('translatable.locales', [config('app.locale')]);
        $fallback = (string) config('translatable.fallback_locale', config('app.fallback_locale', 'en'));

        if (! in_array($fallback, $configuredLocales, true)) {
            $configuredLocales[] = $fallback;
        }

        $this->locales = array_values(array_unique($configuredLocales));
        $this->fallbackLocale = $fallback;

        $this->form->setLocales($this->locales, $this->fallbackLocale);
        $this->form->resetFields();
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
        $this->form->setLocales($this->locales, $this->fallbackLocale);
        $this->form->resetFields();
    }

    public function edit(int $translationId): void
    {
        $translation = UiTranslation::query()->findOrFail($translationId);

        $this->authorize('update', $translation);

        $this->editingId = $translation->id;
        $this->pendingDeletionId = null;
        $this->statusMessage = '';
        $this->form->setLocales($this->locales, $this->fallbackLocale);
        $this->form->translationId = $translation->id;
        $this->form->fillFromArray(
            group: $translation->group,
            key: $translation->key,
            values: $translation->getTranslations('value'),
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
            $this->statusMessage = __('ui.admin.ui_translations.status.deleted');
        }

        if ($this->editingId === $this->pendingDeletionId) {
            $this->startCreate();
        }

        $this->pendingDeletionId = null;
    }

    public function refreshCache(): void
    {
        $this->authorize('update', UiTranslation::class);

        $this->repository()->refreshAndRegister();
        $this->statusMessage = __('ui.admin.ui_translations.status.cache_refreshed');
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

        $this->form->setLocales($this->locales, $this->fallbackLocale);
        $this->form->translationId = $translation?->id;

        $this->form->validate();

        $payload = $this->form->payload();

        if (! $translation) {
            $translation = new UiTranslation;
        }

        $translation->group = $payload['group'];
        $translation->key = $payload['key'];
        $translation->setTranslations('value', $payload['values']);
        $translation->save();

        $this->repository()->refreshAndRegister();

        $this->statusMessage = $this->editingId
            ? __('ui.admin.ui_translations.status.updated')
            : __('ui.admin.ui_translations.status.saved');

        $this->editingId = $translation->id;
        $this->form->translationId = $translation->id;
        $this->form->fillFromArray(
            group: $translation->group,
            key: $translation->key,
            values: $translation->getTranslations('value'),
        );
    }

    protected function repository(): UiTranslationRepository
    {
        return app(UiTranslationRepository::class);
    }
}
