<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UiTranslation;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UiTranslationController extends Controller
{
    /**
     * Display a listing of the translation entries.
     */
    public function index(Request $request): View
    {
        $locales = config('translatable.locales', [config('app.locale')]);
        $search = (string) $request->string('search');

        /** @var LengthAwarePaginator $translations */
        $translations = UiTranslation::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where('key', 'like', "%{$search}%");
            })
            ->orderBy('key')
            ->paginate(15)
            ->withQueryString();

        return view('admin.translations.index', [
            'translations' => $translations,
            'locales' => $locales,
            'search' => $search,
        ]);
    }

    /**
     * Show the form for creating a new translation entry.
     */
    public function create(): View
    {
        $locales = config('translatable.locales', [config('app.locale')]);

        return view('admin.translations.create', [
            'locales' => $locales,
        ]);
    }

    /**
     * Store a newly created translation entry in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $locales = config('translatable.locales', [config('app.locale')]);

        $validated = $request->validate([
            'key' => ['required', 'string', 'max:255', 'unique:ui_translations,key'],
            'text' => ['array'],
            'text.*' => ['nullable', 'string'],
        ]);

        $translation = UiTranslation::create([
            'key' => $validated['key'],
        ]);

        $translation->setTranslations('text', $this->filterTranslationInput($validated['text'] ?? [], $locales));
        $translation->save();

        return redirect()
            ->route('admin.translations.index')
            ->with('status', __('admin.created_success'));
    }

    /**
     * Show the form for editing the specified translation entry.
     */
    public function edit(UiTranslation $translation): View
    {
        $locales = config('translatable.locales', [config('app.locale')]);

        return view('admin.translations.edit', [
            'translation' => $translation,
            'locales' => $locales,
        ]);
    }

    /**
     * Update the specified translation entry in storage.
     */
    public function update(Request $request, UiTranslation $translation): RedirectResponse
    {
        $locales = config('translatable.locales', [config('app.locale')]);

        $validated = $request->validate([
            'key' => ['required', 'string', 'max:255', 'unique:ui_translations,key,' . $translation->id],
            'text' => ['array'],
            'text.*' => ['nullable', 'string'],
        ]);

        $translation->update([
            'key' => $validated['key'],
        ]);

        $translation->setTranslations('text', $this->filterTranslationInput($validated['text'] ?? [], $locales));
        $translation->save();

        return redirect()
            ->route('admin.translations.index')
            ->with('status', __('admin.updated_success'));
    }

    /**
     * Remove the specified translation entry from storage.
     */
    public function destroy(UiTranslation $translation): RedirectResponse
    {
        $translation->delete();

        return redirect()
            ->route('admin.translations.index')
            ->with('status', __('admin.deleted_success'));
    }

    /**
     * Ensure only configured locales are persisted and remove empty strings.
     *
     * @param  array<string, string|null>  $input
     * @param  array<int, string>  $locales
     * @return array<string, string>
     */
    protected function filterTranslationInput(array $input, array $locales): array
    {
        $allowed = array_fill_keys($locales, true);

        return collect($input)
            ->filter(fn ($value, $locale) => isset($allowed[$locale]) && filled($value))
            ->mapWithKeys(fn ($value, $locale) => [$locale => $value])
            ->all();
    }
}
