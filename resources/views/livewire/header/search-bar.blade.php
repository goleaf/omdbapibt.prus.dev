<div class="header-search relative w-full max-w-md" x-data="{ focused: false }">
    <div class="relative">
        <input
            type="search"
            wire:model.live.debounce.300ms="query"
            placeholder="{{ __('ui.nav.search.placeholder') }}"
            x-on:focus="focused = true"
            x-on:blur="focused = false"
            x-on:focus-search-input.window="$el.focus()"
            class="w-full rounded-xl border border-[color:var(--flux-border-soft)] bg-[color:var(--flux-surface-card)] px-4 py-2 pl-10 pr-10 text-sm backdrop-blur-sm transition-all duration-300 placeholder:text-[color:var(--flux-text-muted)] hover:border-emerald-400 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/20"
        />
        
        <!-- Search icon -->
        <div class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[color:var(--flux-text-muted)]">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>

        <!-- Loading spinner -->
        @if ($isLoading)
            <div class="absolute right-3 top-1/2 -translate-y-1/2">
                <svg class="h-4 w-4 animate-spin text-emerald-400" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
        @elseif ($query)
            <!-- Clear button -->
            <button
                type="button"
                wire:click="clear"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-[color:var(--flux-text-muted)] transition hover:text-red-400"
            >
                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
            </button>
        @endif
    </div>

    <!-- Search results dropdown -->
    @if ($showResults && !empty($query))
        <div class="search-results-dropdown absolute top-full mt-2 w-full rounded-xl border border-[color:var(--flux-border-soft)] bg-[color:var(--flux-surface-card)] shadow-xl backdrop-blur-sm">
            @if (empty($results['movies']) && empty($results['shows']) && empty($results['people']))
                <div class="p-6 text-center">
                    <svg class="mx-auto h-12 w-12 text-[color:var(--flux-text-muted)] opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="mt-3 text-sm text-[color:var(--flux-text-muted)]">
                        {{ __('ui.nav.search.no_results') }}
                    </p>
                </div>
            @else
                <div class="max-h-[400px] overflow-y-auto p-2">
                    @if (!empty($results['movies']))
                        <div class="mb-4">
                            <h4 class="mb-2 px-3 text-xs font-semibold uppercase tracking-wider text-emerald-400">Movies</h4>
                            <div class="space-y-1">
                                @foreach ($results['movies'] as $movie)
                                    <a href="{{ $movie['url'] }}" 
                                       wire:click="clear"
                                       class="flex items-center gap-3 rounded-lg px-3 py-2 transition hover:bg-emerald-500/10">
                                        @if ($movie['poster'])
                                            <img src="https://image.tmdb.org/t/p/w92{{ $movie['poster'] }}" 
                                                 alt="{{ $movie['title'] }}"
                                                 class="h-12 w-8 rounded object-cover">
                                        @else
                                            <div class="flex h-12 w-8 items-center justify-center rounded bg-[color:var(--flux-surface-base)] text-xs text-[color:var(--flux-text-muted)]">
                                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                        @endif
                                        <div class="flex-1 overflow-hidden">
                                            <p class="truncate text-sm font-medium text-[color:var(--flux-text)]">{{ $movie['title'] }}</p>
                                            @if ($movie['year'])
                                                <p class="text-xs text-[color:var(--flux-text-muted)]">{{ $movie['year'] }}</p>
                                            @endif
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if (!empty($results['shows']))
                        <div class="mb-4">
                            <h4 class="mb-2 px-3 text-xs font-semibold uppercase tracking-wider text-emerald-400">TV Shows</h4>
                            <div class="space-y-1">
                                @foreach ($results['shows'] as $show)
                                    <a href="{{ $show['url'] }}" 
                                       wire:click="clear"
                                       class="flex items-center gap-3 rounded-lg px-3 py-2 transition hover:bg-emerald-500/10">
                                        @if ($show['poster'])
                                            <img src="https://image.tmdb.org/t/p/w92{{ $show['poster'] }}" 
                                                 alt="{{ $show['title'] }}"
                                                 class="h-12 w-8 rounded object-cover">
                                        @else
                                            <div class="flex h-12 w-8 items-center justify-center rounded bg-[color:var(--flux-surface-base)] text-xs text-[color:var(--flux-text-muted)]">
                                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
                                                </svg>
                                            </div>
                                        @endif
                                        <div class="flex-1 overflow-hidden">
                                            <p class="truncate text-sm font-medium text-[color:var(--flux-text)]">{{ $show['title'] }}</p>
                                            @if ($show['year'])
                                                <p class="text-xs text-[color:var(--flux-text-muted)]">{{ $show['year'] }}</p>
                                            @endif
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if (!empty($results['people']))
                        <div>
                            <h4 class="mb-2 px-3 text-xs font-semibold uppercase tracking-wider text-emerald-400">People</h4>
                            <div class="space-y-1">
                                @foreach ($results['people'] as $person)
                                    <a href="{{ $person['url'] }}" 
                                       wire:click="clear"
                                       class="flex items-center gap-3 rounded-lg px-3 py-2 transition hover:bg-emerald-500/10">
                                        @if ($person['poster'])
                                            <img src="https://image.tmdb.org/t/p/w92{{ $person['poster'] }}" 
                                                 alt="{{ $person['name'] }}"
                                                 class="h-12 w-12 rounded-full object-cover">
                                        @else
                                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-[color:var(--flux-surface-base)] text-xs text-[color:var(--flux-text-muted)]">
                                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                        @endif
                                        <div class="flex-1 overflow-hidden">
                                            <p class="truncate text-sm font-medium text-[color:var(--flux-text)]">{{ $person['name'] }}</p>
                                            @if ($person['department'])
                                                <p class="text-xs text-[color:var(--flux-text-muted)]">{{ $person['department'] }}</p>
                                            @endif
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    @endif
</div>
