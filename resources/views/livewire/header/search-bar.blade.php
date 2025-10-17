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
        <div class="search-results-dropdown absolute top-full mt-2 w-full rounded-xl border border-[color:var(--flux-border-soft)] bg-[color:var(--flux-surface-card)] p-4 shadow-xl backdrop-blur-sm">
            @if (empty($results['movies']) && empty($results['shows']) && empty($results['people']))
                <p class="text-center text-sm text-[color:var(--flux-text-muted)]">
                    {{ __('ui.nav.search.no_results') }}
                </p>
            @else
                <div class="space-y-3">
                    @if (!empty($results['movies']))
                        <div>
                            <h4 class="mb-2 text-xs font-semibold uppercase tracking-wider text-emerald-400">Movies</h4>
                            <!-- Movie results here -->
                        </div>
                    @endif

                    @if (!empty($results['shows']))
                        <div>
                            <h4 class="mb-2 text-xs font-semibold uppercase tracking-wider text-emerald-400">TV Shows</h4>
                            <!-- Show results here -->
                        </div>
                    @endif

                    @if (!empty($results['people']))
                        <div>
                            <h4 class="mb-2 text-xs font-semibold uppercase tracking-wider text-emerald-400">People</h4>
                            <!-- People results here -->
                        </div>
                    @endif
                </div>
            @endif
        </div>
    @endif
</div>
