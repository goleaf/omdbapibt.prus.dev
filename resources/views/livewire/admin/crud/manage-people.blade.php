<div class="grid gap-6 lg:grid-cols-[minmax(0,420px)_1fr]">
    <div class="space-y-4 rounded-3xl border border-slate-800/60 bg-slate-900/70 p-6">
        <div class="flex items-start justify-between">
            <div>
                <h2 class="text-lg font-semibold text-white">{{ __('ui.admin.panel.sections.people.title') }}</h2>
                <p class="mt-1 text-sm text-slate-400">{{ __('ui.admin.panel.sections.people.subtitle') }}</p>
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
                <label class="text-xs uppercase tracking-[0.35em] text-slate-400" for="person-name">{{ __('ui.admin.panel.fields.name') }}</label>
                <input
                    id="person-name"
                    type="text"
                    wire:model.defer="form.name"
                    class="mt-1 w-full rounded-2xl border border-slate-800 bg-slate-950/70 px-4 py-2 text-sm text-slate-100 focus:border-emerald-400 focus:outline-none"
                    placeholder="{{ __('ui.admin.panel.placeholders.person_name') }}"
                />
                @error('form.name')
                    <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="text-xs uppercase tracking-[0.35em] text-slate-400" for="person-slug">{{ __('ui.admin.panel.fields.slug') }}</label>
                <input
                    id="person-slug"
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
                <label class="text-xs uppercase tracking-[0.35em] text-slate-400" for="person-department">{{ __('ui.admin.panel.fields.department') }}</label>
                <input
                    id="person-department"
                    type="text"
                    wire:model.defer="form.known_for_department"
                    class="mt-1 w-full rounded-2xl border border-slate-800 bg-slate-950/70 px-4 py-2 text-sm text-slate-100 focus:border-emerald-400 focus:outline-none"
                    placeholder="{{ __('ui.admin.panel.placeholders.department') }}"
                />
                @error('form.known_for_department')
                    <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="text-xs uppercase tracking-[0.35em] text-slate-400" for="person-birthday">{{ __('ui.admin.panel.fields.birthday') }}</label>
                    <input
                        id="person-birthday"
                        type="date"
                        wire:model.defer="form.birthday"
                        class="mt-1 w-full rounded-2xl border border-slate-800 bg-slate-950/70 px-4 py-2 text-sm text-slate-100 focus:border-emerald-400 focus:outline-none"
                    />
                    @error('form.birthday')
                        <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="text-xs uppercase tracking-[0.35em] text-slate-400" for="person-gender">{{ __('ui.admin.panel.fields.gender') }}</label>
                    <select
                        id="person-gender"
                        wire:model.defer="form.gender"
                        class="mt-1 w-full rounded-2xl border border-slate-800 bg-slate-950/70 px-4 py-2 text-sm text-slate-100 focus:border-emerald-400 focus:outline-none"
                    >
                        <option value="">{{ __('ui.admin.panel.people.gender_unknown') }}</option>
                        <option value="1">{{ __('ui.admin.panel.people.gender_female') }}</option>
                        <option value="2">{{ __('ui.admin.panel.people.gender_male') }}</option>
                        <option value="3">{{ __('ui.admin.panel.people.gender_non_binary') }}</option>
                    </select>
                    @error('form.gender')
                        <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="text-xs uppercase tracking-[0.35em] text-slate-400" for="person-popularity">{{ __('ui.admin.panel.fields.popularity') }}</label>
                <input
                    id="person-popularity"
                    type="number"
                    step="0.01"
                    min="0"
                    wire:model.defer="form.popularity"
                    class="mt-1 w-full rounded-2xl border border-slate-800 bg-slate-950/70 px-4 py-2 text-sm text-slate-100 focus:border-emerald-400 focus:outline-none"
                    placeholder="0.00"
                />
                @error('form.popularity')
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
                    <label class="text-xs uppercase tracking-[0.35em] text-slate-400" for="people-search">{{ __('ui.admin.panel.fields.search') }}</label>
                    <input
                        id="people-search"
                        type="search"
                        wire:model.live="search"
                        placeholder="{{ __('ui.admin.panel.placeholders.search_people') }}"
                        class="mt-1 w-full rounded-full border border-slate-700 bg-slate-950/70 px-4 py-2 text-sm text-slate-100 focus:border-emerald-400 focus:outline-none"
                    />
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-3xl border border-slate-800/60 bg-slate-900/70">
            <table class="min-w-full divide-y divide-slate-800 text-sm">
                <thead class="bg-slate-900/80 text-slate-400">
                    <tr>
                        <th class="px-6 py-3 text-left">{{ __('ui.admin.panel.table.person') }}</th>
                        <th class="px-6 py-3 text-left">{{ __('ui.admin.panel.fields.department') }}</th>
                        <th class="px-6 py-3 text-left">{{ __('ui.admin.panel.fields.birthday') }}</th>
                        <th class="px-6 py-3 text-left">{{ __('ui.admin.panel.fields.popularity') }}</th>
                        <th class="px-6 py-3 text-right">{{ __('ui.admin.panel.table.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800 text-slate-200">
                    @forelse ($this->records as $person)
                        <tr class="hover:bg-slate-900/60" wire:key="person-{{ $person->id }}">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-white">{{ $person->name }}</div>
                                <div class="text-xs text-slate-400">{{ $person->slug }}</div>
                            </td>
                            <td class="px-6 py-4">{{ $person->known_for_department ?? '—' }}</td>
                            <td class="px-6 py-4">{{ optional($person->birthday)->toDateString() ?? '—' }}</td>
                            <td class="px-6 py-4">{{ $person->popularity !== null ? number_format($person->popularity, 2) : '—' }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <button
                                        type="button"
                                        wire:click="edit({{ $person->id }})"
                                        class="rounded-full border border-slate-700 px-3 py-1 text-xs text-slate-100 transition hover:border-emerald-400 hover:text-emerald-200"
                                    >
                                        {{ __('ui.admin.panel.actions.edit') }}
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="delete({{ $person->id }})"
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
