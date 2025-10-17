<x-layouts.app>
    <div class="mx-auto w-full max-w-screen-2xl px-6 py-12 2xl:px-12">
        <div class="mb-8">
            <h1 class="mb-2 text-4xl font-bold text-[color:var(--flux-text-primary)]">
                {{ __('ui.nav.search.button') }}
            </h1>
            @if ($query)
                <p class="text-lg text-[color:var(--flux-text-muted)]">
                    Results for: <span class="font-semibold text-emerald-400">{{ $query }}</span>
                </p>
            @endif
        </div>

        @if (empty($query))
            <div class="rounded-xl border border-[color:var(--flux-border-soft)] bg-[color:var(--flux-surface-card)] p-8 text-center backdrop-blur-sm">
                <svg class="mx-auto h-16 w-16 text-[color:var(--flux-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <p class="mt-4 text-lg text-[color:var(--flux-text-muted)]">
                    {{ __('ui.nav.search.placeholder') }}
                </p>
            </div>
        @elseif (empty($results['movies']) && empty($results['shows']) && empty($results['people']))
            <div class="rounded-xl border border-[color:var(--flux-border-soft)] bg-[color:var(--flux-surface-card)] p-8 text-center backdrop-blur-sm">
                <svg class="mx-auto h-16 w-16 text-[color:var(--flux-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="mt-4 text-lg text-[color:var(--flux-text-muted)]">
                    {{ __('ui.nav.search.no_results') }}
                </p>
            </div>
        @else
            <div class="space-y-8">
                @if (!empty($results['movies']))
                    <section>
                        <h2 class="mb-4 text-2xl font-bold text-[color:var(--flux-text-primary)]">Movies</h2>
                        <!-- Movie results grid here -->
                    </section>
                @endif

                @if (!empty($results['shows']))
                    <section>
                        <h2 class="mb-4 text-2xl font-bold text-[color:var(--flux-text-primary)]">TV Shows</h2>
                        <!-- Show results grid here -->
                    </section>
                @endif

                @if (!empty($results['people']))
                    <section>
                        <h2 class="mb-4 text-2xl font-bold text-[color:var(--flux-text-primary)]">People</h2>
                        <!-- People results grid here -->
                    </section>
                @endif
            </div>
        @endif
    </div>
</x-layouts.app>

