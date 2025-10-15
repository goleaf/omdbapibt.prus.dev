<div class="relative w-full max-w-3xl" wire:click.away="closeResults">
    <label for="global-search" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Search the entire catalog</label>

    <div class="relative mt-2">
        <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400" aria-hidden="true">
            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M8.5 3a5.5 5.5 0 0 1 4.356 8.908l3.118 3.118a1 1 0 0 1-1.414 1.414l-3.118-3.118A5.5 5.5 0 1 1 8.5 3Zm0 2a3.5 3.5 0 1 0 0 7 3.5 3.5 0 0 0 0-7Z" clip-rule="evenodd" />
            </svg>
        </span>

        <input
            id="global-search"
            type="search"
            autocomplete="off"
            placeholder="Search for movies, TV shows, and people"
            class="block w-full rounded-lg border border-slate-300 bg-white py-2.5 pl-11 pr-12 text-base text-slate-900 placeholder:text-slate-500 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-400/70 dark:border-slate-700 dark:bg-slate-900/80 dark:text-slate-100"
            wire:model.live.debounce.400ms="query"
            wire:focus="openResults"
            wire:keydown.arrow-down.prevent="highlightNext"
            wire:keydown.arrow-up.prevent="highlightPrevious"
            wire:keydown.enter.prevent="openActiveResult"
            wire:keydown.escape="closeResults"
            role="combobox"
            aria-expanded="{{ $isOpen ? 'true' : 'false' }}"
            aria-haspopup="listbox"
            aria-autocomplete="list"
            aria-controls="global-search-results"
            aria-activedescendant="{{ $activeIndex >= 0 ? 'global-search-option-' . $activeIndex : '' }}"
        />

        @if($query !== '')
            <button
                type="button"
                class="absolute inset-y-0 right-2 flex items-center rounded-md px-2 text-xs font-medium text-slate-500 transition hover:text-slate-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400 focus-visible:ring-offset-2 focus-visible:ring-offset-white dark:text-slate-400 dark:hover:text-slate-200 dark:focus-visible:ring-offset-slate-900"
                wire:click="clear"
                aria-label="Clear search"
            >
                Esc
            </button>
        @endif
    </div>

    @if($isOpen)
        <div
            id="global-search-results"
            role="listbox"
            aria-label="Search suggestions"
            class="absolute z-20 mt-2 w-full overflow-hidden rounded-lg border border-slate-200 bg-white shadow-xl backdrop-blur dark:border-slate-700/70 dark:bg-slate-900/95"
        >
            @if($this->hasResults)
                <div class="max-h-96 overflow-y-auto py-2" aria-live="polite">
                    @foreach($results as $group => $items)
                        @php($label = $categoryLabels[$group] ?? ucfirst($group))
                        @if(count($items))
                            <div class="pb-1">
                                <p class="px-4 pt-3 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ $label }}</p>
                                <ul role="group" aria-label="{{ $label }}" class="mt-1 space-y-1">
                                    @foreach($items as $item)
                                        <li
                                            id="global-search-option-{{ $item['index'] }}"
                                            role="option"
                                            aria-selected="{{ $activeIndex === $item['index'] ? 'true' : 'false' }}"
                                            wire:key="global-search-{{ $group }}-{{ $item['id'] ?? $item['index'] }}"
                                        >
                                            @php($isActive = $activeIndex === $item['index'])
                                            @php($subtitle = $item['subtitle'] ?? ($label ?? ''))
                                            <a
                                                href="{{ $item['url'] ?? '#' }}"
                                                class="flex items-center gap-3 px-4 py-2 text-sm transition focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400 focus-visible:ring-offset-2 focus-visible:ring-offset-white {{ $isActive ? 'bg-slate-100 text-slate-900 dark:bg-slate-800 dark:text-slate-100' : 'text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800/70' }}"
                                                @if($isActive) aria-current="true" @endif
                                                @if(empty($item['url'])) aria-disabled="true" tabindex="-1" @endif
                                            >
                                                <div class="flex h-12 w-9 shrink-0 items-center justify-center overflow-hidden rounded bg-slate-200 text-xs font-semibold uppercase text-slate-500 dark:bg-slate-700 dark:text-slate-200">
                                                    @if(!empty($item['poster']))
                                                        <img src="{{ $item['poster'] }}" alt="" class="h-full w-full object-cover" loading="lazy">
                                                    @else
                                                        <span aria-hidden="true">{{ mb_substr($item['title'], 0, 1) }}</span>
                                                    @endif
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <p class="truncate font-medium text-slate-900 dark:text-slate-100">{{ $item['title'] }}</p>
                                                    <p class="truncate text-xs text-slate-500 dark:text-slate-400">{{ $subtitle ?: $label }}</p>
                                                </div>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    @endforeach
                </div>
            @else
                <p class="px-4 py-6 text-sm text-slate-500 dark:text-slate-400" role="status">
                    No results for “{{ $query }}”. Try a different search term.
                </p>
            @endif
        </div>
    @endif
</div>
