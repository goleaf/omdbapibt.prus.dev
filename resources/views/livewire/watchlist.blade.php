<div data-locale="{{ $locale }}">
    @if ($toggleMode)
        <div class="flex flex-wrap items-center gap-3">
            @if (! $isAuthenticated)
                <a href="{{ localized_route('login') }}" class="inline-flex items-center gap-2 rounded-md bg-slate-800 px-4 py-2 text-sm font-semibold text-slate-100 transition hover:bg-slate-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-500">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-9A2.25 2.25 0 0 0 2.25 5.25v13.5A2.25 2.25 0 0 0 4.5 21h9a2.25 2.25 0 0 0 2.25-2.25V15" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 12h-7.5" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="m15 15 3-3-3-3" />
                    </svg>
                    {{ __('Sign in to save') }}
                </a>
            @else
                <button type="button" wire:click="toggle" wire:loading.attr="disabled" wire:target="toggle" class="inline-flex items-center gap-2 rounded-md border border-slate-600 px-4 py-2 text-sm font-semibold text-slate-100 transition hover:border-emerald-400 hover:text-emerald-300 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-400">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        @if ($isSaved)
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        @else
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6" />
                        @endif
                    </svg>
                    <span>
                        {{ $isSaved ? __('Remove from Watch Later') : __('Add to Watch Later') }}
                    </span>
                </button>
            @endif
        </div>
    @else
        <section class="space-y-6 rounded-2xl border border-slate-200 bg-white p-6 shadow">
            <header class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-slate-900">{{ __('Your lists') }}</h2>
                    <p class="text-sm text-slate-500">{{ __('Organize titles into private or public collections, then fine-tune the order for sharing or planning.') }}</p>
                </div>
                <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-slate-600">
                    {{ trans_choice(':count title saved|:count titles saved', $summaryCount, ['count' => $summaryCount]) }}
                </span>
            </header>

            @if (! $isAuthenticated)
                <p class="rounded-lg border border-dashed border-slate-300 bg-slate-50 px-4 py-3 text-sm text-slate-600">
                    {{ __('Sign in to start curating personal lists across all your devices.') }}
                </p>
            @else
                <div class="space-y-6">
                    <form wire:submit.prevent="createList" class="flex flex-col gap-3 rounded-lg border border-slate-200 bg-slate-50 p-4 sm:flex-row sm:items-center">
                        <div class="flex-1">
                            <label for="new-list-title" class="block text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('New list title') }}</label>
                            <input id="new-list-title" type="text" wire:model.defer="newListTitle" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400" placeholder="{{ __('Eg. Cozy weekend queue') }}" />
                            @error('newListTitle')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit" wire:loading.attr="disabled" class="inline-flex items-center justify-center rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-500">
                            {{ __('Create list') }}
                        </button>
                    </form>

                    <div class="flex flex-wrap gap-2">
                        @foreach ($lists as $list)
                            <button type="button" wire:click="setActiveList({{ $list['id'] }})" @class([
                                'inline-flex items-center gap-2 rounded-full border px-3 py-1 text-xs font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2',
                                'border-emerald-500 bg-emerald-100 text-emerald-800 focus-visible:outline-emerald-500' => $activeList && $activeList['id'] === $list['id'],
                                'border-slate-200 bg-slate-100 text-slate-600 hover:border-slate-300 hover:text-slate-900 focus-visible:outline-slate-400' => ! $activeList || $activeList['id'] !== $list['id'],
                            ])>
                                <span>{{ $list['title'] }}</span>
                                @if ($list['public'])
                                    <svg class="h-3 w-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h12A2.25 2.25 0 0 1 20.25 6v12A2.25 2.25 0 0 1 18 20.25H6A2.25 2.25 0 0 1 3.75 18V6z" />
                                    </svg>
                                @endif
                            </button>
                        @endforeach
                    </div>

                    @if ($activeList)
                        <div class="space-y-4 rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                            <div class="flex flex-col gap-3 border-b border-slate-200 pb-4 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-slate-900">{{ $activeList['title'] }}</h3>
                                    <p class="text-xs uppercase tracking-wide text-slate-500">
                                        {{ $activeList['public'] ? __('Public list') : __('Private list') }}
                                    </p>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    <button type="button" wire:click="togglePrivacy({{ $activeList['id'] }})" wire:loading.attr="disabled" class="inline-flex items-center gap-2 rounded-md border border-slate-200 px-3 py-1 text-xs font-semibold text-slate-700 transition hover:border-emerald-400 hover:text-emerald-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-400">
                                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15.75a3.75 3.75 0 1 0 0-7.5 3.75 3.75 0 0 0 0 7.5z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12a7.5 7.5 0 1 1-15 0 7.5 7.5 0 0 1 15 0z" />
                                        </svg>
                                        {{ $activeList['public'] ? __('Make private') : __('Make public') }}
                                    </button>
                                    <button type="button" wire:click="startRenaming({{ $activeList['id'] }})" class="inline-flex items-center gap-2 rounded-md border border-slate-200 px-3 py-1 text-xs font-semibold text-slate-700 transition hover:border-slate-300 hover:text-slate-900 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-400">
                                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487 19.5 7.125m-2.638-2.638-9.515 9.515a4.5 4.5 0 0 0-1.168 2.054l-.447 1.79a.75.75 0 0 0 .91.91l1.79-.448a4.5 4.5 0 0 0 2.054-1.168l9.515-9.515m-2.638-2.638-2.025-2.025a2.121 2.121 0 0 0-3 0l-9.193 9.193a6 6 0 0 0-1.56 2.754l-.458 1.833a1.5 1.5 0 0 0 1.822 1.822l1.833-.458a6 6 0 0 0 2.754-1.56l9.193-9.193a2.121 2.121 0 0 0 0-3z" />
                                        </svg>
                                        {{ __('Rename') }}
                                    </button>
                                    <button type="button" wire:click="deleteList({{ $activeList['id'] }})" wire:loading.attr="disabled" class="inline-flex items-center gap-2 rounded-md border border-rose-200 px-3 py-1 text-xs font-semibold text-rose-600 transition hover:border-rose-300 hover:text-rose-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-rose-400">
                                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673A2.25 2.25 0 0 1 15.916 21.75H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.06.68-.114 1.022-.165m0 0L5.25 4.772A2.25 2.25 0 0 1 7.5 3.75h9a2.25 2.25 0 0 1 2.25 1.022l.478.791m-13.5-.001a48.11 48.11 0 0 1 3.478-.398M9.75 3V4.5m4.5-1.5V4.5" />
                                        </svg>
                                        {{ __('Delete') }}
                                    </button>
                                </div>
                            </div>

                            @if ($renamingListId === $activeList['id'])
                                <form wire:submit.prevent="updateListTitle" class="flex flex-col gap-3 rounded-lg border border-slate-200 bg-slate-50 p-4">
                                    <label for="rename-list" class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Rename list') }}</label>
                                    <input id="rename-list" type="text" wire:model.defer="renamingTitle" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400" />
                                    @error('renamingTitle')
                                        <p class="text-xs text-rose-600">{{ $message }}</p>
                                    @enderror
                                    <div class="flex flex-wrap gap-2">
                                        <button type="submit" class="inline-flex items-center justify-center rounded-md bg-emerald-600 px-4 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-emerald-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-500">
                                            {{ __('Save') }}
                                        </button>
                                        <button type="button" wire:click="$set('renamingListId', null)" class="inline-flex items-center justify-center rounded-md border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-600 transition hover:border-slate-300 hover:text-slate-900 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-400">
                                            {{ __('Cancel') }}
                                        </button>
                                    </div>
                                </form>
                            @endif

                            @if (count($activeList['items']) === 0)
                                <p class="rounded-lg border border-dashed border-slate-300 bg-slate-50 px-4 py-4 text-sm text-slate-600">
                                    {{ __('This list is empty. Add a movie from the catalog to get started.') }}
                                </p>
                            @else
                                <ul class="space-y-3">
                                    @foreach ($activeList['items'] as $item)
                                        <li wire:key="list-item-{{ $item['id'] }}" class="flex flex-col gap-3 rounded-lg border border-slate-200 bg-slate-50 p-4 sm:flex-row sm:items-center sm:justify-between">
                                            <div>
                                                <a href="{{ $item['slug'] ? route('movies.show', ['locale' => $locale, 'movie' => $item['slug']]) : '#' }}" class="text-sm font-semibold text-slate-900 hover:text-emerald-600">
                                                    {{ $item['title'] }}
                                                </a>
                                                <p class="text-xs text-slate-500">
                                                    {{ $item['year'] ? __('Released :year', ['year' => $item['year']]) : __('Release year unknown') }}
                                                </p>
                                            </div>
                                            <div class="flex flex-wrap items-center gap-2">
                                                <button type="button" wire:click="moveItemUp({{ $item['id'] }})" class="inline-flex items-center rounded-md border border-slate-200 px-2 py-1 text-xs font-semibold text-slate-600 transition hover:border-slate-300 hover:text-slate-900 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-400" title="{{ __('Move up') }}">
                                                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 15.75 7.5-7.5 7.5 7.5" />
                                                    </svg>
                                                </button>
                                                <button type="button" wire:click="moveItemDown({{ $item['id'] }})" class="inline-flex items-center rounded-md border border-slate-200 px-2 py-1 text-xs font-semibold text-slate-600 transition hover:border-slate-300 hover:text-slate-900 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-400" title="{{ __('Move down') }}">
                                                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                                    </svg>
                                                </button>
                                                <button type="button" wire:click="removeItem({{ $item['id'] }})" class="inline-flex items-center gap-1 rounded-md border border-transparent bg-slate-900 px-3 py-1 text-xs font-semibold text-white transition hover:bg-slate-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-500">
                                                    {{ __('Remove') }}
                                                </button>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    @endif
                </div>
            @endif
        </section>
    @endif
</div>
