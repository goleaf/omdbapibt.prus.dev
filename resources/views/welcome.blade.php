@extends('layouts.app', [
    'title' => config('app.name', 'OMDb Stream'),
])

@section('content')
    <div class="space-y-20">
        <!-- Hero Section -->
        <div class="relative overflow-hidden rounded-3xl border border-[color:var(--flux-border-soft)] bg-gradient-to-br from-[color:var(--flux-surface-card)] via-[color:var(--flux-surface-backdrop)] to-[color:var(--flux-surface-card)] p-8 shadow-2xl backdrop-blur-2xl sm:p-12 lg:p-16">
            <!-- Background Effects -->
            <div class="pointer-events-none absolute -top-32 right-1/4 h-96 w-96 rounded-full bg-gradient-to-br from-emerald-500/25 to-emerald-600/15 blur-3xl motion-soft-glow"></div>
            <div class="pointer-events-none absolute -bottom-20 left-1/4 h-80 w-80 rounded-full bg-gradient-to-br from-blue-500/20 to-blue-600/10 blur-3xl motion-float"></div>
            
            <div class="relative">
                <div class="mb-6 flex items-start justify-between">
                    <div class="inline-flex items-center gap-2 rounded-full border border-emerald-400/40 bg-emerald-500/10 px-4 py-1.5 text-sm font-bold uppercase tracking-wider text-emerald-300 shadow-lg shadow-emerald-500/20 backdrop-blur-sm">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Powered by Flux
                    </div>
                    <x-theme-toggle class="md:hidden" />
                </div>

                <div class="grid gap-12 lg:grid-cols-2 lg:items-center">
                    <div class="space-y-8">
                        <div class="space-y-4">
                            <h1 class="bg-gradient-to-r from-white via-slate-100 to-slate-300 bg-clip-text text-4xl font-bold leading-tight tracking-tight text-transparent sm:text-5xl lg:text-6xl">
                                Cinematic intelligence
                            </h1>
                            <p class="text-xl font-semibold text-emerald-400">Livewire + Flux driven movie discovery</p>
                            <p class="max-w-xl text-lg leading-relaxed text-slate-300">
                                Power your watchlists with a design system tuned for rich imagery, live previews, and accessible motion.
                                Tailwind cinematic utilities keep every card, badge, and tab in sync across light and dark themes.
                            </p>
                        </div>
                        <div class="flex flex-col gap-4 sm:flex-row">
                            <a href="{{ localized_route('browse') }}" class="group inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-emerald-500 to-emerald-600 px-6 py-3 font-bold text-white shadow-lg shadow-emerald-500/30 transition-all duration-300 hover:scale-105 hover:shadow-xl hover:shadow-emerald-500/40 hover:from-emerald-400 hover:to-emerald-500">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M8 5v14l11-7z"/>
                                </svg>
                                <span>{{ __('ui.nav.links.browse') }}</span>
                            </a>
                            <a href="{{ localized_route('pricing') }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-[color:var(--flux-border-soft)] bg-[color:var(--flux-surface-card)] px-6 py-3 font-semibold backdrop-blur-sm transition-all duration-300 hover:border-emerald-400 hover:bg-emerald-500/10 hover:text-emerald-400 hover:scale-105 hover:shadow-lg">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                                </svg>
                                {{ __('ui.nav.links.pricing') }}
                            </a>
                        </div>
                    </div>

                    <div class="cinematic-backdrop">
                        <div class="relative space-y-6">
                            <div class="flex items-center justify-between">
                                <x-flux.rating-badge :score="9.1" label="IMDb" />
                                <span class="inline-flex items-center gap-2 rounded-full border border-emerald-400/30 bg-emerald-500/10 px-3 py-1 text-xs font-bold uppercase tracking-widest text-emerald-300">
                                    <span class="relative flex h-2 w-2">
                                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span>
                                        <span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-500"></span>
                                    </span>
                                    Live
                                </span>
                            </div>
                            <p class="text-base leading-relaxed flux-text-muted">
                                Theme-aware gradients, soft glow animations, and screen-reader safe badges let your catalogs shine
                                without sacrificing performance or clarity.
                            </p>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="group rounded-2xl border border-[color:var(--flux-border-soft)] bg-[var(--flux-surface-card)] p-5 backdrop-blur-sm transition-all duration-300 hover:scale-105 hover:border-emerald-400/50 hover:shadow-lg">
                                    <p class="text-xs font-bold uppercase tracking-widest text-emerald-300">Design tokens</p>
                                    <p class="mt-2 text-lg font-bold">Flux + Tailwind</p>
                                </div>
                                <div class="group rounded-2xl border border-[color:var(--flux-border-soft)] bg-[var(--flux-surface-card)] p-5 backdrop-blur-sm transition-all duration-300 hover:scale-105 hover:border-emerald-400/50 hover:shadow-lg">
                                    <p class="text-xs font-bold uppercase tracking-widest text-emerald-300">Motion</p>
                                    <p class="mt-2 text-lg font-bold">Cosmic ease</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features Grid -->
        <section class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <div class="flux-card group" data-elevated="false">
                <div class="relative space-y-4">
                    <div class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500/20 to-emerald-600/10 text-emerald-400">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect width="18" height="18" x="3" y="3" rx="2"/>
                            <path d="M3 9h18"/>
                            <path d="M9 21V9"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold">Reusable cards</h3>
                        <p class="mt-1 text-sm text-emerald-400/80">Drop into dashboards, hero rails, and watchlists</p>
                    </div>
                    <p class="text-sm leading-relaxed flux-text-muted">
                        Flux cards wrap tailwind primitives with gradient guardrails and interactive accents. Drop badges, buttons,
                        charts, or any Livewire component inside without restyling from scratch.
                    </p>
                </div>
            </div>

            <div class="flux-card group" data-elevated="false">
                <div class="relative space-y-4">
                    <div class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500/20 to-blue-600/10 text-blue-400">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold">Adaptive badges</h3>
                        <p class="mt-1 text-sm text-blue-400/80">Communicate ratings, runtimes, and languages</p>
                    </div>
                    <p class="text-sm leading-relaxed flux-text-muted">
                        Rating badges render crisp numerics, label translations, and warm or cool variants so your UI always matches
                        the metadata context.
                    </p>
                    <div class="flex flex-wrap gap-3 pt-2">
                        <x-flux.rating-badge :score="8.4" label="IMDb" />
                        <x-flux.rating-badge :score="95" tone="warm" label="Audience" />
                    </div>
                </div>
            </div>

            <div class="flux-card group" data-elevated="false">
                <div class="relative space-y-4">
                    <div class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-purple-500/20 to-purple-600/10 text-purple-400">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="3"/>
                            <path d="M12 1v6m0 6v6m8.66-13.66l-4.24 4.24m-8.48 8.48L3.7 22.3m19.6-19.6l-4.24 4.24M7.94 16.94l-4.24 4.24"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold">Theme controls</h3>
                        <p class="mt-1 text-sm text-purple-400/80">Persisted across locales, tabs, and devices</p>
                    </div>
                    <p class="text-sm leading-relaxed flux-text-muted">
                        The theme toggle persists user intent, respects system defaults, and updates transition-friendly gradients
                        without layout jank.
                    </p>
                    <div class="pt-2">
                        <x-theme-toggle />
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
