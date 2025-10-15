<div class="space-y-10">
    <header class="flex flex-col gap-4 border-b border-slate-800 pb-6 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h1 class="text-3xl font-semibold text-slate-50">UI translation manager</h1>
            <p class="mt-1 text-sm text-slate-400">
                Manage localized interface copy across supported locales and sync updates to the Redis-backed cache.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <button
                type="button"
                wire:click="startCreate"
                class="inline-flex items-center gap-2 rounded-full border border-slate-700 px-4 py-2 text-sm font-medium text-slate-200 transition hover:border-emerald-400 hover:text-emerald-200"
            >
                <span class="inline-flex h-2 w-2 rounded-full bg-emerald-400"></span>
                New translation
            </button>
            <button
                type="button"
                wire:click="refreshCache"
                wire:loading.attr="disabled"
                class="inline-flex items-center gap-2 rounded-full border border-emerald-500/60 px-4 py-2 text-sm font-medium text-emerald-300 transition hover:border-emerald-300 hover:text-emerald-100 disabled:cursor-not-allowed disabled:border-slate-700 disabled:text-slate-500"
            >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992m0 0V4.356m0 4.992-3.181-3.181A8.25 8.25 0 1 0 20.486 15" />
                </svg>
                <span wire:loading.remove>Refresh cache</span>
                <span wire:loading>Refreshing…</span>
            </button>
        </div>
    </header>

    @if ($statusMessage !== '')
        <div class="rounded-xl border border-emerald-500/40 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
            {{ $statusMessage }}
        </div>
    @endif

    <section class="grid gap-8 lg:grid-cols-[minmax(0,380px)_minmax(0,1fr)]">
        <div class="rounded-3xl border border-slate-800 bg-slate-900/70 p-6 shadow-xl shadow-emerald-500/10">
            <h2 class="text-lg font-semibold text-slate-100">
                {{ $editingId ? 'Edit translation' : 'Create translation' }}
            </h2>
            <p class="mt-1 text-sm text-slate-400">
                Define the translation group, key, and localized values. The fallback locale is required for every entry.
            </p>

            <form wire:submit.prevent="save" class="mt-5 space-y-5">
                <div class="space-y-1.5">
                    <label for="translation-group" class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                        Group
                    </label>
                    <input
                        id="translation-group"
                        type="text"
                        wire:model.defer="form.group"
                        class="w-full rounded-xl border border-slate-700 bg-slate-950/60 px-4 py-2 text-sm text-slate-100 focus:border-emerald-400 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                        placeholder="nav"
                    >
                    @error('form.group')
                        <p class="text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-1.5">
                    <label for="translation-key" class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                        Key
                    </label>
                    <input
                        id="translation-key"
                        type="text"
                        wire:model.defer="form.key"
                        class="w-full rounded-xl border border-slate-700 bg-slate-950/60 px-4 py-2 text-sm text-slate-100 focus:border-emerald-400 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                        placeholder="cta_label"
                    >
                    @error('form.key')
                        <p class="text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-4">
                    @foreach ($locales as $locale)
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold uppercase tracking-wide text-slate-400" for="translation-{{ $locale }}">
                                {{ strtoupper($locale) }}
                                @if ($locale === $this->fallbackLocale)
                                    <span class="text-red-400">*</span>
                                @endif
                            </label>
                            <textarea
                                id="translation-{{ $locale }}"
                                rows="2"
                                wire:model.defer="form.values.{{ $locale }}"
                                class="w-full rounded-xl border border-slate-700 bg-slate-950/60 px-4 py-2 text-sm text-slate-100 focus:border-emerald-400 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                            ></textarea>
                            @error('form.values.' . $locale)
                                <p class="text-xs text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    @endforeach
                </div>

                <div class="flex items-center justify-end gap-3">
                    @if ($editingId)
                        <button
                            type="button"
                            wire:click="startCreate"
                            class="rounded-full border border-slate-700 px-4 py-2 text-sm font-medium text-slate-300 transition hover:border-slate-500 hover:text-slate-100"
                        >
                            Cancel edit
                        </button>
                    @endif
                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center gap-2 rounded-full bg-emerald-500 px-5 py-2 text-sm font-semibold text-emerald-950 transition hover:bg-emerald-400 disabled:cursor-not-allowed disabled:bg-slate-700 disabled:text-slate-400"
                    >
                        <span wire:loading.remove>Save translation</span>
                        <span wire:loading>Saving…</span>
                    </button>
                </div>
            </form>
        </div>

        <div class="rounded-3xl border border-slate-800 bg-slate-900/70 p-6 shadow-xl shadow-slate-900/40">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-slate-100">Stored translations</h2>
                <span class="rounded-full border border-slate-700 px-3 py-1 text-xs uppercase tracking-wide text-slate-500">
                    {{ count($locales) }} locales
                </span>
            </div>

            <div class="mt-5 overflow-hidden rounded-2xl border border-slate-800">
                <table class="min-w-full divide-y divide-slate-800 text-sm">
                    <thead class="bg-slate-950/80">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-slate-400">Group</th>
                            <th class="px-4 py-3 text-left font-semibold text-slate-400">Key</th>
                            @foreach ($locales as $locale)
                                <th class="px-4 py-3 text-left font-semibold text-slate-400">{{ strtoupper($locale) }}</th>
                            @endforeach
                            <th class="px-4 py-3 text-right font-semibold text-slate-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 bg-slate-950/40">
                        @forelse ($translations as $translation)
                            <tr wire:key="translation-{{ $translation['id'] }}" class="hover:bg-slate-900/60">
                                <td class="px-4 py-3 font-mono text-xs uppercase tracking-wide text-slate-400">{{ $translation['group'] }}</td>
                                <td class="px-4 py-3 font-mono text-xs uppercase tracking-wide text-slate-400">{{ $translation['key'] }}</td>
                                @foreach ($locales as $locale)
                                    <td class="px-4 py-3 text-slate-200">
                                        {{ $translation['previews'][$locale] ?? '—' }}
                                    </td>
                                @endforeach
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        <button
                                            type="button"
                                            wire:click="edit({{ $translation['id'] }})"
                                            class="rounded-full border border-slate-700 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-slate-200 transition hover:border-emerald-400 hover:text-emerald-200"
                                        >
                                            Edit
                                        </button>
                                        @if ($pendingDeletionId === $translation['id'])
                                            <button
                                                type="button"
                                                wire:click="deleteConfirmed"
                                                class="rounded-full border border-red-500/70 bg-red-500/10 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-red-200 transition hover:border-red-400 hover:text-red-100"
                                            >
                                                Confirm
                                            </button>
                                            <button
                                                type="button"
                                                wire:click="cancelDeletion"
                                                class="rounded-full border border-slate-700 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-slate-300 transition hover:border-slate-500 hover:text-slate-100"
                                            >
                                                Cancel
                                            </button>
                                        @else
                                            <button
                                                type="button"
                                                wire:click="confirmDeletion({{ $translation['id'] }})"
                                                class="rounded-full border border-slate-700 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-slate-300 transition hover:border-red-400 hover:text-red-200"
                                            >
                                                Delete
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ 3 + count($locales) }}" class="px-4 py-6 text-center text-sm text-slate-400">
                                    No UI translations have been created yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
