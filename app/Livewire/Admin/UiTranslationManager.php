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

        $this->form->configure($this->locales, $this->fallbackLocale);
        $this->form->startCreating();
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
        $this->form->startCreating();
    }

    public function edit(int $translationId): void
    {
        $translation = UiTranslation::query()->findOrFail($translationId);

        $this->authorize('update', $translation);

        $this->editingId = $translation->id;
        $this->pendingDeletionId = null;
        $this->statusMessage = '';

        $this->form->fillFromModel($translation);
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
            $this->statusMessage = trans('admin.ui_translations.status.deleted');
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
        $this->statusMessage = trans('admin.ui_translations.status.cache_refreshed');
    }

    public function save(): void
    {
        $translation = null;
        $wasEditing = (bool) $this->editingId;

        if ($this->editingId) {
            $translation = UiTranslation::query()->find($this->editingId);

            if ($translation) {
                $this->authorize('update', $translation);
                $this->form->setTranslationId($translation->id);
            } else {
                $this->authorize('create', UiTranslation::class);
                $this->form->setTranslationId(null);
            }
        } else {
            $this->authorize('create', UiTranslation::class);
            $this->form->setTranslationId(null);
        }

        $this->statusMessage = '';
        $savedTranslation = $this->form->persist($translation);

        $this->repository()->refreshAndRegister();

        $this->statusMessage = $wasEditing
            ? trans('admin.ui_translations.status.updated')
            : trans('admin.ui_translations.status.saved');

        $this->editingId = $savedTranslation->id;
    }

    protected function repository(): UiTranslationRepository
    {
        return app(UiTranslationRepository::class);
    }
}
