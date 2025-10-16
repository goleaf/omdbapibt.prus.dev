<div class="grid gap-6 lg:grid-cols-[minmax(0,360px)_1fr]">
    <div class="space-y-4 rounded-3xl border border-slate-800/60 bg-slate-900/70 p-6">
        <div class="flex items-start justify-between">
            <div>
                <h2 class="text-lg font-semibold text-white">{{ __('ui.admin.panel.sections.genres.title') }}</h2>
                <p class="mt-1 text-sm text-slate-400">{{ __('ui.admin.panel.sections.genres.subtitle') }}</p>
            </div>
            <button
                type="button"
                wire:click="create"
                class="rounded-full border border-slate-700 px-3 py-1 text-xs text-slate-200 transition hover:border-emerald-400 hover:text-emerald-200"
            >
                {{ __('ui.admin.panel.actions.reset') }}
            </button>
        </div>

        <form wire:submit.prevent="save" class="space-y-4">
            <div>
                <label class="text-xs uppercase tracking-[0.35em] text-slate-400" for="genre-name">{{ __('ui.admin.panel.fields.name') }}</label>
                <input
                    id="genre-name"
                    type="text"
                    wire:model.defer="form.name"
                    class="mt-1 w-full rounded-2xl border border-slate-800 bg-slate-950/70 px-4 py-2 text-sm text-slate-100 focus:border-emerald-400 focus:outline-none"
                    placeholder="{{ __('ui.admin.panel.placeholders.genre_name') }}"
                />
                @error('form.name')
                    <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="text-xs uppercase tracking-[0.35em] text-slate-400" for="genre-slug">{{ __('ui.admin.panel.fields.slug') }}</label>
                <input
                    id="genre-slug"
                    type="text"
                    wire:model.defer="form.slug"
                    class="mt-1 w-full rounded-2xl border border-slate-800 bg-slate-950/70 px-4 py-2 text-sm text-slate-100 focus:border-emerald-400 focus:outline-none"
                    placeholder="{{ __('ui.admin.panel.placeholders.slug') }}"
                />
                @error('form.slug')
                    <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="text-xs uppercase tracking-[0.35em] text-slate-400" for="genre-tmdb">{{ __('ui.admin.panel.fields.tmdb_id') }}</label>
                <input
                    id="genre-tmdb"
                    type="number"
                    min="0"
                    wire:model.defer="form.tmdb_id"
                    class="mt-1 w-full rounded-2xl border border-slate-800 bg-slate-950/70 px-4 py-2 text-sm text-slate-100 focus:border-emerald-400 focus:outline-none"
                    placeholder="{{ __('ui.admin.panel.placeholders.tmdb_id') }}"
                />
                @error('form.tmdb_id')
                    <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button
                    type="submit"
                    class="rounded-full bg-emerald-500 px-4 py-2 text-sm font-semibold text-emerald-950 transition hover:bg-emerald-400"
                >
                    {{ $editingId ? __('ui.admin.panel.actions.update') : __('ui.admin.panel.actions.create') }}
                </button>
                <button
                    type="button"
                    wire:click="create"
                    class="rounded-full border border-slate-700 px-4 py-2 text-sm text-slate-200 transition hover:border-emerald-400 hover:text-emerald-200"
                >
                    {{ __('ui.admin.panel.actions.clear') }}
                </button>
            </div>
        </form>
    </div>

    <div class="space-y-4">
        <div class="rounded-3xl border border-slate-800/60 bg-slate-900/70 p-4">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div class="w-full sm:max-w-xs">
                    <label class="text-xs uppercase tracking-[0.35em] text-slate-400" for="genre-search">{{ __('ui.admin.panel.fields.search') }}</label>
                    <input
                        id="genre-search"
                        type="search"
                        wire:model.live="search"
                        placeholder="{{ __('ui.admin.panel.placeholders.search_genres') }}"
                        class="mt-1 w-full rounded-full border border-slate-700 bg-slate-950/70 px-4 py-2 text-sm text-slate-100 focus:border-emerald-400 focus:outline-none"
                    />
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-3xl border border-slate-800/60 bg-slate-900/70">
            <table class="min-w-full divide-y divide-slate-800 text-sm">
                <thead class="bg-slate-900/80 text-slate-400">
                    <tr>
                        <th class="px-6 py-3 text-left">{{ __('ui.admin.panel.table.genre') }}</th>
                        <th class="px-6 py-3 text-left">{{ __('ui.admin.panel.fields.slug') }}</th>
                        <th class="px-6 py-3 text-left">{{ __('ui.admin.panel.fields.tmdb_id') }}</th>
                        <th class="px-6 py-3 text-right">{{ __('ui.admin.panel.table.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800 text-slate-200">
                    @forelse ($this->records as $genre)
                        <tr class="hover:bg-slate-900/60" wire:key="genre-{{ $genre->id }}">
                            <td class="px-6 py-4 font-semibold text-white">{{ $genre->localizedName('en') }}</td>
                            <td class="px-6 py-4">{{ $genre->slug }}</td>
                            <td class="px-6 py-4">{{ $genre->tmdb_id ?? '—' }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <button
                                        type="button"
                                        wire:click="edit({{ $genre->id }})"
                                        class="rounded-full border border-slate-700 px-3 py-1 text-xs text-slate-100 transition hover:border-emerald-400 hover:text-emerald-200"
                                    >
                                        {{ __('ui.admin.panel.actions.edit') }}
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="delete({{ $genre->id }})"
                                        class="rounded-full border border-rose-500/40 px-3 py-1 text-xs text-rose-200 transition hover:border-rose-400 hover:text-rose-100"
                                    >
                                        {{ __('ui.admin.panel.actions.delete') }}
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-slate-400">{{ __('ui.admin.panel.table.empty') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>
            {{ $this->records->links() }}
        </div>
    </div>
</div>
