@php
    $spotlight = [
        [
            'title' => 'Nebula Run',
            'meta' => '2024 • 2h 12m • Sci-Fi Adventure',
            'image' => 'https://image.tmdb.org/t/p/w500/1syW9SNna38rSl9fnXwc9fP7POW.jpg',
            'rating' => 8.4,
            'tag' => 'New',
            'highlight' => true,
        ],
        [
            'title' => 'Velvet Shadows',
            'meta' => '2023 • 1h 58m • Noir Thriller',
            'image' => 'https://image.tmdb.org/t/p/w500/zCViszfnKfJIWAX2SVzq8NNJgoj.jpg',
            'rating' => 7.7,
            'tag' => 'Trending',
            'highlight' => false,
        ],
        [
            'title' => 'Arcadia Protocol',
            'meta' => '2024 • 2h 21m • Cyberpunk Mystery',
            'image' => 'https://image.tmdb.org/t/p/w500/8uUU2pxm6IYZw8UgnKJyx7Dqwu9.jpg',
            'rating' => 9.1,
            'tag' => 'Editor’s pick',
            'highlight' => true,
        ],
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Laravel') }} • Flux UI Preview</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="flux-shell">
            <div class="absolute inset-0 flux-grid opacity-50"></div>
            <header class="relative mx-auto flex max-w-6xl flex-col gap-16 px-6 pb-20 pt-16 lg:flex-row lg:items-center lg:gap-24">
                <div class="max-w-xl space-y-6">
                    <span class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-4 py-2 text-xs font-semibold uppercase tracking-[0.4em] text-white/80">
                        Flux UI + Tailwind 4
                    </span>
                    <h1 class="text-balance text-4xl font-bold text-white drop-shadow-[0_35px_35px_rgba(15,23,42,0.45)] sm:text-5xl">
                        Build cinematic experiences with reusable Livewire Flux components.
                    </h1>
                    <p class="max-w-xl text-lg leading-relaxed text-slate-200/85">
                        A custom theme that blends Netflix-inspired presentation with IMDb-style trust signals. Toggle between light and dark mode, remix the tokens, and reuse the cards across your movie dashboards.
                    </p>
                    <div class="flex flex-wrap items-center gap-4">
                        <a href="{{ url('/ui/components') }}" class="flux-button">
                            Explore component guide
                        </a>
                        <x-flux.theme-toggle />
                    </div>
                    <dl class="grid grid-cols-2 gap-6 pt-6 text-white/80 sm:grid-cols-4">
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-white/60">Theme presets</dt>
                            <dd class="text-2xl font-semibold">24</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-white/60">Flux components</dt>
                            <dd class="text-2xl font-semibold">15+</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-white/60">Dark mode opt-ins</dt>
                            <dd class="text-2xl font-semibold">Automatic</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-white/60">Livewire ready</dt>
                            <dd class="text-2xl font-semibold">Yes</dd>
                        </div>
                    </dl>
                </div>
                <div class="relative w-full max-w-md space-y-6">
                    <div class="glass-panel card-highlight rounded-3xl border border-white/20 p-6 shadow-2xl shadow-brand-500/20">
                        <p class="text-sm uppercase tracking-[0.3em] text-brand-100">In the spotlight</p>
                        <h2 class="mt-3 text-2xl font-semibold text-white">Tonight’s featured line-up</h2>
                        <p class="mt-3 text-sm text-slate-200/70">
                            Dynamic layout powered by Flux cards, rating badges, and glass morphic backdrops.
                        </p>
                        <div class="mt-6 space-y-6">
                            @foreach ($spotlight as $feature)
                                <x-flux.card
                                    :title="$feature['title']"
                                    :meta="$feature['meta']"
                                    :image="$feature['image']"
                                    :rating="$feature['rating']"
                                    :tag="$feature['tag']"
                                    :highlight="$feature['highlight']"
                                />
                            @endforeach
                        </div>
                    </div>
                </div>
            </header>
        </div>
        <main class="relative -mt-24 bg-surface-50 pb-24 pt-32 text-surface-900 dark:bg-surface-950 dark:text-surface-100">
            <div class="mx-auto flex max-w-6xl flex-col gap-20 px-6">
                <section class="space-y-6">
                    <div class="flex flex-wrap items-center justify-between gap-6">
                        <div>
                            <h2 class="text-3xl font-semibold">Reusable card grid</h2>
                            <p class="mt-2 text-sm text-surface-500 dark:text-surface-300">
                                Drop the Flux card component into Livewire lists, trending rails, or curated carousels.
                            </p>
                        </div>
                        <a href="{{ url('/ui/components#cards') }}" class="flux-button" data-variant="ghost">
                            View guidelines
                        </a>
                    </div>
                    <div class="grid gap-6 md:grid-cols-3">
                        @foreach ($spotlight as $feature)
                            <x-flux.card
                                :title="$feature['title']"
                                :meta="$feature['meta']"
                                :image="$feature['image']"
                                :rating="$feature['rating']"
                                :tag="$feature['tag']"
                                :highlight="$feature['highlight']"
                                class="bg-surface-900/90 dark:bg-surface-900"
                            />
                        @endforeach
                    </div>
                </section>
                <section class="space-y-6">
                    <div class="flex flex-wrap items-center justify-between gap-6">
                        <div>
                            <h2 class="text-3xl font-semibold">IMDb inspired rating badges</h2>
                            <p class="mt-2 text-sm text-surface-500 dark:text-surface-300">
                                Signal trust quickly with neon and gold palettes that adapt to your theme tokens.
                            </p>
                        </div>
                        <div class="flex flex-wrap gap-3">
                            <x-flux.rating-badge score="8.9" label="IMDb" />
                            <x-flux.rating-badge score="97" label="Audience" variant="highlight" />
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </body>
</html>
