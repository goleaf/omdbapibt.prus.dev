<div class="space-y-8">
    <header class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">UI Translations</h1>
            <p class="mt-1 text-sm text-gray-500">
                Manage localized strings that power navigation, dashboard messaging, and discovery filters.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <button
                type="button"
                wire:click="startCreating"
                class="rounded-lg border border-indigo-200 bg-white px-4 py-2 text-sm font-medium text-indigo-600 transition hover:border-indigo-400 hover:text-indigo-700"
            >
                New translation
            </button>
        </div>
    </header>

    @if (session('status'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
            {{ session('status') }}
        </div>
    @endif

    <div class="grid gap-8 lg:grid-cols-3">
        <section class="lg:col-span-2">
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h2 class="text-lg font-semibold text-gray-800">Existing translations</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                        <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500">
                            <tr>
                                <th class="px-6 py-3">Group</th>
                                <th class="px-6 py-3">Key</th>
                                <th class="px-6 py-3">Values</th>
                                <th class="px-6 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse ($translations as $translation)
                                <tr wire:key="translation-{{ $translation->id }}">
                                    <td class="px-6 py-4 font-medium text-gray-800">{{ $translation->group }}</td>
                                    <td class="px-6 py-4 text-gray-700">{{ $translation->key }}</td>
                                    <td class="px-6 py-4">
                                        <dl class="space-y-1 text-xs text-gray-600">
                                            @foreach ($this->locales as $locale)
                                                <div class="flex items-start gap-2">
                                                    <dt class="mt-0.5 w-10 font-semibold text-gray-700">{{ strtoupper($locale) }}</dt>
                                                    <dd class="flex-1 text-gray-600">{{ $translation->getTranslation('value', $locale, false) }}</dd>
                                                </div>
                                            @endforeach
                                        </dl>
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm">
                                        <div class="flex justify-end gap-3">
                                            <button
                                                type="button"
                                                wire:click="startEditing({{ $translation->id }})"
                                                class="text-indigo-600 transition hover:text-indigo-800"
                                            >
                                                Edit
                                            </button>
                                            <button
                                                type="button"
                                                wire:click="deleteTranslation({{ $translation->id }})"
                                                class="text-red-600 transition hover:text-red-800"
                                            >
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-6 text-center text-gray-500">
                                        No translations have been created yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <section>
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-800">
                    {{ $form['id'] ? 'Edit translation' : 'Create translation' }}
                </h2>
                <form class="mt-4 space-y-4" wire:submit.prevent="save">
                    <div>
                        <label for="translation-group" class="block text-sm font-medium text-gray-700">Group</label>
                        <input
                            id="translation-group"
                            type="text"
                            wire:model.defer="form.group"
                            class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        >
                        @error('form.group')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="translation-key" class="block text-sm font-medium text-gray-700">Key</label>
                        <input
                            id="translation-key"
                            type="text"
                            wire:model.defer="form.key"
                            class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        >
                        @error('form.key')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-4">
                        @foreach ($this->locales as $locale)
                            <div>
                                <label for="translation-{{ $locale }}" class="block text-sm font-medium text-gray-700">
                                    {{ strtoupper($locale) }} value
                                </label>
                                <textarea
                                    id="translation-{{ $locale }}"
                                    wire:model.defer="form.translations.{{ $locale }}"
                                    rows="2"
                                    class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                                ></textarea>
                                @error('form.translations.' . $locale)
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        @endforeach
                    </div>

                    <div class="flex items-center justify-between">
                        @if ($form['id'])
                            <button
                                type="button"
                                wire:click="startCreating"
                                class="text-sm text-gray-500 transition hover:text-gray-700"
                            >
                                Cancel edit
                            </button>
                        @endif
                        <button
                            type="submit"
                            class="ml-auto inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-500"
                        >
                            {{ $form['id'] ? 'Update translation' : 'Create translation' }}
                        </button>
                    </div>
                </form>
            </div>
        </section>
    </div>
</div>
