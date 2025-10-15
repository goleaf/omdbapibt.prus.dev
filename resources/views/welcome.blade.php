@extends('layouts.app', [
    'title' => config('app.name', 'OMDb Stream'),
])

@section('content')
    <div class="space-y-16">
        <x-flux.card heading="Cinematic intelligence" subheading="Livewire + Flux driven movie discovery" elevated>
            <x-slot:actions>
                <x-theme-toggle class="md:hidden" />
            </x-slot:actions>

            <div class="grid gap-10 lg:grid-cols-2 lg:items-center">
                <div class="space-y-6">
                    <p class="text-base flux-text-muted">
                        Power your watchlists with a design system tuned for rich imagery, live previews, and accessible motion.
                        Tailwind cinematic utilities keep every card, badge, and tab in sync across light and dark themes.
                    </p>
                    <div class="flex flex-col gap-3 sm:flex-row">
                        <flux:button href="{{ route('browse') }}" variant="primary" color="emerald" icon-leading="play">
                            {{ __('ui.nav.links.browse') }}
                        </flux:button>
                        <flux:button href="{{ route('pricing') }}" variant="ghost" icon-leading="sparkles">
                            {{ __('ui.nav.links.pricing') }}
                        </flux:button>
                    </div>
                </div>
                <div class="cinematic-backdrop">
                    <div class="relative space-y-6">
                        <div class="flex items-center justify-between">
                            <x-flux.rating-badge :score="9.1" label="Rating" />
                            <span class="text-xs uppercase tracking-[0.35em] text-emerald-200">Flux motion presets</span>
                        </div>
                        <p class="text-sm flux-text-muted">
                            Theme-aware gradients, soft glow animations, and screen-reader safe badges let your catalogs shine
                            without sacrificing performance or clarity.
                        </p>
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div class="rounded-2xl border border-[color:var(--flux-border-soft)] bg-[var(--flux-surface-card)] p-4">
                                <p class="text-xs uppercase tracking-[0.35em] text-emerald-300">Design tokens</p>
                                <p class="mt-2 text-lg font-semibold">Flux + Tailwind</p>
                            </div>
                            <div class="rounded-2xl border border-[color:var(--flux-border-soft)] bg-[var(--flux-surface-card)] p-4">
                                <p class="text-xs uppercase tracking-[0.35em] text-emerald-300">Motion</p>
                                <p class="mt-2 text-lg font-semibold">Cosmic ease</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </x-flux.card>

        <section class="cinematic-grid" data-compact="true">
            <x-flux.card heading="Reusable cards" subheading="Drop into dashboards, hero rails, and watchlists">
                <p class="text-sm flux-text-muted">
                    Flux cards wrap tailwind primitives with gradient guardrails and interactive accents. Drop badges, buttons,
                    charts, or any Livewire component inside without restyling from scratch.
                </p>
            </x-flux.card>
            <x-flux.card heading="Adaptive badges" subheading="Communicate ratings, runtimes, and languages">
                <p class="text-sm flux-text-muted">
                    Rating badges render crisp numerics, label translations, and warm or cool variants so your UI always matches
                    the metadata context.
                </p>
                <div class="mt-4 flex flex-wrap gap-3">
                    <x-flux.rating-badge :score="8.4" label="IMDb" />
                    <x-flux.rating-badge :score="95" tone="warm" label="Audience" />
                </div>
            </x-flux.card>
            <x-flux.card heading="Theme controls" subheading="Persisted across locales, tabs, and devices">
                <p class="text-sm flux-text-muted">
                    The theme toggle persists user intent, respects system defaults, and updates transition-friendly gradients
                    without layout jank.
                </p>
                <div class="mt-4">
                    <x-theme-toggle />
                </div>
            </x-flux.card>
        </section>
    </div>
@endsection
