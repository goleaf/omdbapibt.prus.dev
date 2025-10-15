@extends('layouts.app', [
    'title' => 'Flux cinematic starter',
])

@section('content')
    <section class="space-y-16">
        <div class="grid gap-10 lg:grid-cols-[1.1fr,0.9fr]">
            <x-flux.card padding="p-8" eyebrow="Livewire + Flux UI">
                <div class="space-y-6">
                    <div class="space-y-4">
                        <h1 class="text-4xl font-semibold tracking-tight text-slate-900 dark:text-white sm:text-5xl">
                            Configure once. Ship cinematic experiences.
                        </h1>
                        <p class="text-base text-slate-600 dark:text-slate-300">
                            Flux design tokens, Tailwind utilities, and reusable components are bundled so every page can feel
                            like a premiere. Toggle themes, experiment with gradients, and remix layouts without leaving
                            Blade.
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-3 text-xs font-semibold uppercase tracking-[0.32em]">
                        <a
                            href="{{ route('browse') }}"
                            class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-emerald-400 via-cyan-400 to-blue-500 px-6 py-2 text-slate-900 shadow-[var(--shadow-glow)] transition hover:brightness-110"
                        >
                            Explore the catalogue
                        </a>
                        <a
                            href="{{ route('ui.components') }}"
                            class="inline-flex items-center gap-2 rounded-full border border-slate-400/40 bg-white/70 px-6 py-2 text-slate-700 transition hover:border-emerald-400/70 hover:text-emerald-500 dark:border-white/15 dark:bg-slate-900/70 dark:text-slate-200"
                        >
                            Preview components
                        </a>
                    </div>
                    <div class="grid gap-5 sm:grid-cols-3">
                        <div class="rounded-2xl border border-slate-200/70 bg-white/70 p-4 text-sm text-slate-600 shadow-sm backdrop-blur-md dark:border-white/10 dark:bg-slate-900/70 dark:text-slate-300">
                            <p class="text-[0.6rem] font-semibold uppercase tracking-[0.45em] text-slate-500 dark:text-slate-400">Launch-ready tokens</p>
                            <p class="mt-3 text-2xl font-semibold text-slate-900 dark:text-white">65+</p>
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Colors, shadows, radii, and timing curves.</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200/70 bg-white/70 p-4 text-sm text-slate-600 shadow-sm backdrop-blur-md dark:border-white/10 dark:bg-slate-900/70 dark:text-slate-300">
                            <p class="text-[0.6rem] font-semibold uppercase tracking-[0.45em] text-slate-500 dark:text-slate-400">Blade macros</p>
                            <p class="mt-3 text-2xl font-semibold text-slate-900 dark:text-white">3 new</p>
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Cards, rating badges, and a theme toggle.</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200/70 bg-white/70 p-4 text-sm text-slate-600 shadow-sm backdrop-blur-md dark:border-white/10 dark:bg-slate-900/70 dark:text-slate-300">
                            <p class="text-[0.6rem] font-semibold uppercase tracking-[0.45em] text-slate-500 dark:text-slate-400">Dark-mode checks</p>
                            <p class="mt-3 text-2xl font-semibold text-slate-900 dark:text-white">100%</p>
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Styles respond instantly to theme changes.</p>
                        </div>
                    </div>
                </div>
                <div class="space-y-6">
                    <div class="flex items-center gap-3">
                        <x-flux.rating-badge :value="9.4" label="Audience" />
                        <p class="text-sm text-slate-600 dark:text-slate-300">Average satisfaction after enabling cinematic mode.</p>
                    </div>
                    <div class="rounded-2xl border border-white/30 bg-white/60 p-5 shadow-inner backdrop-blur-xl dark:border-white/10 dark:bg-slate-950/60">
                        <p class="text-xs font-semibold uppercase tracking-[0.4em] text-slate-500 dark:text-slate-400">Cinematic preset</p>
                        <div class="mt-4 grid gap-3 text-sm text-slate-600 dark:text-slate-300">
                            <div class="flex items-center justify-between">
                                <span>Gradient layers</span>
                                <span class="font-semibold text-slate-900 dark:text-white">3</span>
                            </div>
                            <div class="flux-divider"></div>
                            <div class="flex items-center justify-between">
                                <span>Glass blur</span>
                                <span class="font-semibold text-slate-900 dark:text-white">22px</span>
                            </div>
                            <div class="flux-divider"></div>
                            <div class="flex items-center justify-between">
                                <span>Motion curve</span>
                                <span class="font-semibold text-slate-900 dark:text-white">0.33, 1, 0.68, 1</span>
                            </div>
                        </div>
                    </div>
                </div>
            </x-flux.card>

            <x-flux.card padding="p-0" :interactive="false" class="cinematic-gradient">
                <div class="space-y-8 p-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.45em] text-slate-500 dark:text-slate-300">Theme aware</p>
                            <h2 class="mt-3 text-2xl font-semibold text-slate-900 dark:text-white">Flip the lights</h2>
                        </div>
                        <x-flux.theme-toggle />
                    </div>
                    <p class="text-sm text-slate-600 dark:text-slate-300">
                        Theme state persists with local storage and respects system preferences. Every component leans on
                        semantic tokens so the palette feels intentional in both modes.
                    </p>
                    <div class="space-y-4 rounded-2xl border border-white/30 bg-white/70 p-6 shadow-[var(--shadow-card)] backdrop-blur-xl dark:border-white/10 dark:bg-slate-950/70">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold uppercase tracking-[0.38em] text-slate-500 dark:text-slate-400">Preview</span>
                            <span class="flux-shimmer h-px w-24 rounded-full"></span>
                        </div>
                        <x-flux.card padding="p-5" :interactive="false" class="!bg-white/80 dark:!bg-slate-900/60">
                            <div class="space-y-3">
                                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Hero CTA card</h3>
                                <p class="text-sm text-slate-600 dark:text-slate-300">
                                    Responsive padding, optional media slots, and gradient overlays on hover.
                                </p>
                                <div class="flex flex-wrap gap-2">
                                    <span class="rounded-full border border-slate-300/60 px-3 py-1 text-xs uppercase tracking-[0.3em] text-slate-500 dark:border-white/20 dark:text-slate-300">Flux panel</span>
                                    <span class="rounded-full border border-slate-300/60 px-3 py-1 text-xs uppercase tracking-[0.3em] text-slate-500 dark:border-white/20 dark:text-slate-300">Glass blur</span>
                                </div>
                            </div>
                        </x-flux.card>
                    </div>
                </div>
            </x-flux.card>
        </div>

        <section class="grid gap-8 md:grid-cols-3">
            <x-flux.card :interactive="false" padding="p-6">
                <div class="space-y-3">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Flux design tokens</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-300">
                        Tailwind 4 tokens lock in consistent color, motion, and spacing primitives. Utilities such as
                        <code class="rounded bg-white/60 px-1 py-0.5 text-xs text-slate-700 dark:bg-white/10 dark:text-slate-200">flux-container</code>
                        keep your grid cinematic on any screen.
                    </p>
                </div>
            </x-flux.card>
            <x-flux.card :interactive="false" padding="p-6">
                <div class="space-y-3">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Reusable components</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-300">
                        Blade-powered Flux card, rating badge, and theme toggle ship with accessible defaults and Livewire
                        friendly props for rapid prototyping.
                    </p>
                </div>
            </x-flux.card>
            <x-flux.card :interactive="false" padding="p-6">
                <div class="space-y-3">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white">UI components guide</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-300">
                        Visit <a href="{{ route('ui.components') }}" class="underline decoration-emerald-400/60 underline-offset-4">/ui/components</a>
                        for interactive previews, live code snippets, and theme-aware demos.
                    </p>
                </div>
            </x-flux.card>
        </section>
    </section>
@endsection
