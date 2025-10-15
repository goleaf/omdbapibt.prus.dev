@extends('layouts.app', [
    'title' => 'Flux-powered cinematic discovery',
])

@section('content')
    <div class="space-y-16">
        <section class="relative overflow-hidden rounded-[2.75rem] border border-slate-800/60 bg-gradient-to-br from-slate-950 via-slate-900 to-slate-900 p-8 shadow-2xl ring-1 ring-emerald-500/15 sm:p-12">
            <div class="pointer-events-none absolute -top-24 right-12 h-64 w-64 rounded-full bg-emerald-500/20 blur-3xl"></div>
            <div class="pointer-events-none absolute bottom-0 left-1/3 h-72 w-72 rounded-full bg-emerald-400/10 blur-3xl"></div>
            <div class="relative flex flex-col gap-10 lg:flex-row lg:items-center">
                <div class="max-w-xl space-y-6">
                    <flux:badge variant="solid" color="emerald">Livewire + Flux launch</flux:badge>
                    <h1 class="text-4xl font-bold text-white sm:text-5xl lg:text-6xl">A mobile-first command center for your watchlists</h1>
                    <p class="text-base text-slate-300 sm:text-lg">
                        Discover premieres, surface deep cuts, and audit availability in one responsive dashboard. Flux UI drives the layout while Livewire keeps every interaction instantaneous across devices.
                    </p>
                    <div class="flex flex-col gap-3 sm:flex-row">
                        <flux:button href="{{ route('browse') }}" variant="primary" color="emerald" icon-leading="play">
                            Start browsing
                        </flux:button>
                        <flux:button href="{{ route('pricing') }}" variant="ghost" icon-leading="sparkles">
                            View membership tiers
                        </flux:button>
                    </div>
                </div>
                <div class="flex-1 rounded-3xl border border-white/10 bg-white/5 p-6 shadow-inner backdrop-blur">
                    <h2 class="text-sm font-semibold uppercase tracking-[0.35em] text-emerald-200">Realtime highlights</h2>
                    <dl class="mt-5 grid gap-4 text-slate-100 sm:grid-cols-3">
                        <div class="space-y-2 rounded-2xl border border-white/10 bg-white/10 p-4">
                            <dt class="text-xs uppercase tracking-[0.35em] text-emerald-200">Streaming regions</dt>
                            <dd class="text-2xl font-semibold">87</dd>
                        </div>
                        <div class="space-y-2 rounded-2xl border border-white/10 bg-white/10 p-4">
                            <dt class="text-xs uppercase tracking-[0.35em] text-emerald-200">Flux-enabled components</dt>
                            <dd class="text-2xl font-semibold">42</dd>
                        </div>
                        <div class="space-y-2 rounded-2xl border border-white/10 bg-white/10 p-4">
                            <dt class="text-xs uppercase tracking-[0.35em] text-emerald-200">Library uptime</dt>
                            <dd class="text-2xl font-semibold">99.9%</dd>
                        </div>
                    </dl>
                    <p class="mt-6 text-xs text-emerald-100/80">Updated {{ now()->format('M j, Y') }} across all catalog sources.</p>
                </div>
            </div>
        </section>

        @livewire('landing.catalog-browser')

        <section class="rounded-[2.5rem] border border-slate-800/60 bg-slate-950/70 p-8 shadow-xl ring-1 ring-white/5 sm:p-12">
            <div class="grid gap-10 lg:grid-cols-3">
                <div class="space-y-3">
                    <h2 class="text-2xl font-semibold text-white">Livewire-native controls</h2>
                    <p class="text-sm text-slate-300">Sidebar toggles, infinite scroll, and curated filters all run server-powered with no hydration lag.</p>
                </div>
                <div class="space-y-3">
                    <h2 class="text-2xl font-semibold text-white">Flux design system</h2>
                    <p class="text-sm text-slate-300">Buttons, badges, and layout primitives stay consistent from mobile stacks to cinematic desktops.</p>
                </div>
                <div class="space-y-3">
                    <h2 class="text-2xl font-semibold text-white">Curated catalog config</h2>
                    <p class="text-sm text-slate-300">Adjust featured slates, genres, and ratings centrally without rewriting component logic.</p>
                </div>
            </div>
        </section>
    </div>
@endsection
