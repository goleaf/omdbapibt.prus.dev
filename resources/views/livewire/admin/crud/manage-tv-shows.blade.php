<div class="grid gap-6 lg:grid-cols-[minmax(0,420px)_1fr]">
    <div class="space-y-4 rounded-3xl border border-slate-800/60 bg-slate-900/70 p-6">
        <div class="flex items-start justify-between">
            <div>
                <h2 class="text-lg font-semibold text-white">{{ __('ui.admin.panel.sections.tv_shows.title') }}</h2>
                <p class="mt-1 text-sm text-slate-400">{{ __('ui.admin.panel.sections.tv_shows.subtitle') }}</p>
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
                <label class="text-xs uppercase tracking-[0.35em] text-slate-400" for="show-name">{{ __('ui.admin.panel.fields.name') }}</label>
                <input
                    id="show-name"
                    type="text"
                    wire:model.defer="form.name"
                    class="mt-1 w-full rounded-2xl border border-slate-800 bg-slate-950/70 px-4 py-2 text-sm text-slate-100 focus:border-emerald-400 focus:outline-none"
                    placeholder="{{ __('ui.admin.panel.placeholders.show_name') }}"
                />
                @error('form.name')
                    <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="text-xs uppercase tracking-[0.35em] text-slate-400" for="show-slug">{{ __('ui.admin.panel.fields.slug') }}</label>
                <input
                    id="show-slug"
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
                <label class="text-xs uppercase tracking-[0.35em] text-slate-400" for="show-status">{{ __('ui.admin.panel.fields.status') }}</label>
                <input
                    id="show-status"
                    type="text"
                    wire:model.defer="form.status"
                    class="mt-1 w-full rounded-2xl border border-slate-800 bg-slate-950/70 px-4 py-2 text-sm text-slate-100 focus:border-emerald-400 focus:outline-none"
                    placeholder="{{ __('ui.admin.panel.placeholders.status') }}"
                />
                @error('form.status')
                    <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="text-xs uppercase tracking-[0.35em] text-slate-400" for="show-first-air">{{ __('ui.admin.panel.fields.first_air_date') }}</label>
                    <input
                        id="show-first-air"
                        type="date"
                        wire:model.defer="form.first_air_date"
                        class="mt-1 w-full rounded-2xl border border-slate-800 bg-slate-950/70 px-4 py-2 text-sm text-slate-100 focus:border-emerald-400 focus:outline-none"
                    />
                    @error('form.first_air_date')
                        <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="text-xs uppercase tracking-[0.35em] text-slate-400" for="show-vote">{{ __('ui.admin.panel.fields.vote_average') }}</label>
                    <input
                        id="show-vote"
                        type="number"
                        step="0.1"
                        min="0"
                        max="10"
                        wire:model.defer="form.vote_average"
                        class="mt-1 w-full rounded-2xl border border-slate-800 bg-slate-950/70 px-4 py-2 text-sm text-slate-100 focus:border-emerald-400 focus:outline-none"
                        placeholder="0.0"
                    />
                    @error('form.vote_average')
                        <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center gap-3">
                <input
                    id="show-adult"
                    type="checkbox"
                    wire:model.defer="form.adult"
                    class="h-4 w-4 rounded border-slate-700 bg-slate-900 text-emerald-400 focus:ring-emerald-400"
                />
                <label for="show-adult" class="text-sm text-slate-300">{{ __('ui.admin.panel.fields.adult') }}</label>
            </div>
            @error('form.adult')
                <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
            @enderror

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
                    <label class="text-xs uppercase tracking-[0.35em] text-slate-400" for="show-search">{{ __('ui.admin.panel.fields.search') }}</label>
                    <input
                        id="show-search"
                        type="search"
                        wire:model.live="search"
                        placeholder="{{ __('ui.admin.panel.placeholders.search_shows') }}"
                        class="mt-1 w-full rounded-full border border-slate-700 bg-slate-950/70 px-4 py-2 text-sm text-slate-100 focus:border-emerald-400 focus:outline-none"
                    />
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-3xl border border-slate-800/60 bg-slate-900/70">
            <table class="min-w-full divide-y divide-slate-800 text-sm">
                <thead class="bg-slate-900/80 text-slate-400">
                    <tr>
                        <th class="px-6 py-3 text-left">{{ __('ui.admin.panel.table.show') }}</th>
                        <th class="px-6 py-3 text-left">{{ __('ui.admin.panel.fields.status') }}</th>
                        <th class="px-6 py-3 text-left">{{ __('ui.admin.panel.fields.first_air_date') }}</th>
                        <th class="px-6 py-3 text-left">{{ __('ui.admin.panel.fields.vote_average') }}</th>
                        <th class="px-6 py-3 text-right">{{ __('ui.admin.panel.table.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800 text-slate-200">
                    @forelse ($this->records as $show)
                        <tr class="hover:bg-slate-900/60" wire:key="show-{{ $show->id }}">
                            <td class="px-6 py-4">
                                @php
                                    $displayName = $show->name ?? data_get($show->name_translations, 'en');
                                @endphp
                                <div class="font-semibold text-white">{{ $displayName ?: '—' }}</div>
                                <div class="text-xs text-slate-400">{{ $show->slug }}</div>
                            </td>
                            <td class="px-6 py-4">{{ $show->status ?? '—' }}</td>
                            <td class="px-6 py-4">{{ optional($show->first_air_date)->toDateString() ?? '—' }}</td>
                            <td class="px-6 py-4">{{ $show->vote_average !== null ? number_format($show->vote_average, 1) : '—' }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <button
                                        type="button"
                                        wire:click="edit({{ $show->id }})"
                                        class="rounded-full border border-slate-700 px-3 py-1 text-xs text-slate-100 transition hover:border-emerald-400 hover:text-emerald-200"
                                    >
                                        {{ __('ui.admin.panel.actions.edit') }}
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="delete({{ $show->id }})"
                                        class="rounded-full border border-rose-500/40 px-3 py-1 text-xs text-rose-200 transition hover:border-rose-400 hover:text-rose-100"
                                    >
                                        {{ __('ui.admin.panel.actions.delete') }}
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400">{{ __('ui.admin.panel.table.empty') }}</td>
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
