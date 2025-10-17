<div class="grid gap-6 lg:grid-cols-[minmax(0,360px)_1fr]">
    <div class="space-y-4 rounded-3xl border border-slate-800/60 bg-slate-900/70 p-6">
        <div class="flex items-start justify-between">
            <div>
                <h2 class="text-lg font-semibold text-white">{{ __('ui.admin.panel.sections.tags.title') }}</h2>
                <p class="mt-1 text-sm text-slate-400">{{ __('ui.admin.panel.sections.tags.subtitle') }}</p>
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
            <div class="space-y-3">
                <div>
                    <label class="text-xs uppercase tracking-[0.35em] text-slate-400" for="tag-name-en">{{ __('ui.admin.panel.tags.fields.name_en') }}</label>
                    <input
                        id="tag-name-en"
                        type="text"
                        wire:model.defer="form.name.en"
                        class="mt-1 w-full rounded-2xl border border-slate-800 bg-slate-950/70 px-4 py-2 text-sm text-slate-100 focus:border-emerald-400 focus:outline-none"
                        placeholder="{{ __('ui.admin.panel.tags.placeholders.name_en') }}"
                    />
                    @error('form.name.en')
                        <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="text-xs uppercase tracking-[0.35em] text-slate-400" for="tag-name-es">{{ __('ui.admin.panel.tags.fields.name_es') }}</label>
                    <input
                        id="tag-name-es"
                        type="text"
                        wire:model.defer="form.name.es"
                        class="mt-1 w-full rounded-2xl border border-slate-800 bg-slate-950/70 px-4 py-2 text-sm text-slate-100 focus:border-emerald-400 focus:outline-none"
                        placeholder="{{ __('ui.admin.panel.tags.placeholders.name_es') }}"
                    />
                    @error('form.name.es')
                        <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="text-xs uppercase tracking-[0.35em] text-slate-400" for="tag-name-fr">{{ __('ui.admin.panel.tags.fields.name_fr') }}</label>
                    <input
                        id="tag-name-fr"
                        type="text"
                        wire:model.defer="form.name.fr"
                        class="mt-1 w-full rounded-2xl border border-slate-800 bg-slate-950/70 px-4 py-2 text-sm text-slate-100 focus:border-emerald-400 focus:outline-none"
                        placeholder="{{ __('ui.admin.panel.tags.placeholders.name_fr') }}"
                    />
                    @error('form.name.fr')
                        <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="text-xs uppercase tracking-[0.35em] text-slate-400" for="tag-slug">{{ __('ui.admin.panel.fields.slug') }}</label>
                <input
                    id="tag-slug"
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
                <label class="text-xs uppercase tracking-[0.35em] text-slate-400" for="tag-type">{{ __('ui.admin.panel.tags.fields.type') }}</label>
                <select
                    id="tag-type"
                    wire:model.defer="form.type"
                    class="mt-1 w-full rounded-2xl border border-slate-800 bg-slate-950/70 px-4 py-2 text-sm text-slate-100 focus:border-emerald-400 focus:outline-none"
                >
                    @foreach (\App\Models\Tag::TYPES as $type)
                        <option value="{{ $type }}">{{ __('ui.admin.panel.tags.types.'.$type) }}</option>
                    @endforeach
                </select>
                @error('form.type')
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

        <div class="rounded-3xl border border-slate-800/60 bg-slate-950/40 p-4">
            <h3 class="text-sm font-semibold text-white">{{ __('ui.admin.panel.tags.merge.title') }}</h3>
            <p class="text-xs text-slate-400">{{ __('ui.admin.panel.tags.merge.subtitle') }}</p>

            <form wire:submit.prevent="merge" class="mt-3 space-y-3">
                <div>
                    <label class="text-xs uppercase tracking-[0.35em] text-slate-400" for="merge-source">{{ __('ui.admin.panel.tags.merge.source') }}</label>
                    <input
                        id="merge-source"
                        type="number"
                        min="1"
                        wire:model.defer="merge.source_id"
                        class="mt-1 w-full rounded-2xl border border-slate-800 bg-slate-950/70 px-4 py-2 text-sm text-slate-100 focus:border-emerald-400 focus:outline-none"
                        placeholder="{{ __('ui.admin.panel.tags.merge.placeholders.source') }}"
                    />
                    @error('merge.source_id')
                        <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="text-xs uppercase tracking-[0.35em] text-slate-400" for="merge-target">{{ __('ui.admin.panel.tags.merge.target') }}</label>
                    <input
                        id="merge-target"
                        type="number"
                        min="1"
                        wire:model.defer="merge.target_id"
                        class="mt-1 w-full rounded-2xl border border-slate-800 bg-slate-950/70 px-4 py-2 text-sm text-slate-100 focus:border-emerald-400 focus:outline-none"
                        placeholder="{{ __('ui.admin.panel.tags.merge.placeholders.target') }}"
                    />
                    @error('merge.target_id')
                        <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                    @enderror
                </div>
                <button
                    type="submit"
                    class="w-full rounded-full border border-purple-400 px-4 py-2 text-sm font-semibold text-purple-100 transition hover:bg-purple-500/20"
                >
                    {{ __('ui.admin.panel.tags.merge.action') }}
                </button>
            </form>
        </div>
    </div>

    <div class="space-y-4">
        <div class="rounded-3xl border border-slate-800/60 bg-slate-900/70 p-4">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div class="w-full sm:max-w-xs">
                    <label class="text-xs uppercase tracking-[0.35em] text-slate-400" for="tag-search">{{ __('ui.admin.panel.fields.search') }}</label>
                    <input
                        id="tag-search"
                        type="search"
                        wire:model.live="search"
                        placeholder="{{ __('ui.admin.panel.tags.placeholders.search') }}"
                        class="mt-1 w-full rounded-full border border-slate-700 bg-slate-950/70 px-4 py-2 text-sm text-slate-100 focus:border-emerald-400 focus:outline-none"
                    />
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-3xl border border-slate-800/60 bg-slate-900/70">
            <table class="min-w-full divide-y divide-slate-800 text-sm">
                <thead class="bg-slate-900/80 text-slate-400">
                    <tr>
                        <th class="px-6 py-3 text-left">{{ __('ui.admin.panel.tags.table.tag') }}</th>
                        <th class="px-6 py-3 text-left">{{ __('ui.admin.panel.fields.slug') }}</th>
                        <th class="px-6 py-3 text-left">{{ __('ui.admin.panel.tags.table.type') }}</th>
                        <th class="px-6 py-3 text-right">{{ __('ui.admin.panel.tags.table.usage') }}</th>
                        <th class="px-6 py-3 text-right">{{ __('ui.admin.panel.table.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800 text-slate-200">
                    @forelse ($this->records as $tag)
                        <tr class="hover:bg-slate-900/60" wire:key="tag-{{ $tag->id }}">
                            <td class="px-6 py-4 font-semibold text-white">{{ $tag->localizedName('en') }}</td>
                            <td class="px-6 py-4">{{ $tag->slug }}</td>
                            <td class="px-6 py-4 capitalize">{{ __('ui.admin.panel.tags.types.'.$tag->type) }}</td>
                            <td class="px-6 py-4 text-right">{{ number_format($tag->movies_count) }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <button
                                        type="button"
                                        wire:click="edit({{ $tag->id }})"
                                        class="rounded-full border border-slate-700 px-3 py-1 text-xs text-slate-100 transition hover:border-emerald-400 hover:text-emerald-200"
                                    >
                                        {{ __('ui.admin.panel.actions.edit') }}
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="delete({{ $tag->id }})"
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
