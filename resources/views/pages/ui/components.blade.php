@extends('layouts.app', [
    'title' => 'UI Components',
    'header' => 'Flux UI component reference',
    'subheader' => 'Preview reusable cards, badges, and cinematic utilities available across the OMDb interface.',
])

@section('content')
    <div class="space-y-12">
        <x-flux.card heading="Flux card variations" subheading="Interactive containers with gradient motion">
            <div class="grid gap-6 md:grid-cols-2">
                <x-flux.card heading="Default state" subheading="Lightweight border with hover gradient">
                    <p class="text-sm flux-text-muted">
                        Combine with <code class="rounded border border-[color:var(--flux-border-soft)] bg-[var(--flux-surface-card)] px-1.5">cinematic-grid</code>
                        to build responsive rails. The <code class="rounded border border-[color:var(--flux-border-soft)] bg-[var(--flux-surface-card)] px-1.5">elevated</code>
                        attribute applies a subtle glow shadow for featured content.
                    </p>
                </x-flux.card>
                <x-flux.card heading="Elevated" subheading="Hero ready callout" elevated>
                    <p class="text-sm flux-text-muted">
                        Add <code>elevated</code> to surface important metrics or curated rows. Motion presets keep transitions
                        smooth while respecting reduced motion preferences.
                    </p>
                </x-flux.card>
            </div>
        </x-flux.card>

        <x-flux.card heading="Rating badge palettes" subheading="Warm and cool tones for flexible contexts">
            <div class="flex flex-wrap gap-4">
                <x-flux.rating-badge :score="7.8" label="Critics" />
                <x-flux.rating-badge :score="92" tone="warm" label="Audience" />
                <x-flux.rating-badge :score="4.5" label="Flux" />
            </div>
        </x-flux.card>

        <x-flux.card heading="Theme controls" subheading="Persistent color scheme toggling">
            <p class="text-sm flux-text-muted">
                The toggle honors the user’s preferred scheme, falls back to system defaults, and stores overrides in
                <code class="rounded border border-[color:var(--flux-border-soft)] bg-[var(--flux-surface-card)] px-1.5">localStorage</code>.
                Use anywhere in navigation, dashboards, or
                modals.
            </p>
            <div class="mt-4 flex flex-wrap items-center gap-4">
                <x-theme-toggle />
                <span class="text-xs uppercase tracking-[0.35em] text-emerald-200">Try me</span>
            </div>
        </x-flux.card>
    </div>
@endsection
