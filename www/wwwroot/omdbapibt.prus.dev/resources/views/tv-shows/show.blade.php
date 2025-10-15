<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $show->name ?? 'TV Show Details' }} &mdash; {{ config('app.name', 'Laravel') }}</title>
    @vite('resources/css/app.css')
    @livewireStyles
</head>
<body class="antialiased bg-slate-950 text-slate-100">
    <div class="min-h-screen">
        <header class="bg-slate-900/70 backdrop-blur">
            <div class="mx-auto max-w-6xl px-6 py-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-3xl font-semibold">{{ $show->name ?? 'Unknown Title' }}</h1>
                    @if (!empty($show->tagline))
                        <p class="text-slate-300">{{ $show->tagline }}</p>
                    @endif
                </div>
                <div class="flex items-center gap-4">
                    <span class="rounded-full bg-slate-800 px-3 py-1 text-xs uppercase tracking-wider text-slate-300">TV Show</span>
                    <livewire:watchlist watchable-type="tv_show" :watchable-id="$show->id" :show-list="false" />
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-6xl px-6 py-10 grid gap-8 md:grid-cols-[240px,1fr]">
            @if (!empty($show->poster_path))
                <div>
                    <img
                        src="{{ $show->poster_path }}"
                        alt="{{ $show->name ?? $show->original_name }} poster"
                        class="w-full rounded-lg shadow-lg"
                    >
                </div>
            @endif

            <div class="space-y-6">
                @if (!empty($show->overview))
                    <section class="rounded-lg bg-slate-900/60 p-6 shadow">
                        <h2 class="text-lg font-semibold text-white">Series Overview</h2>
                        <p class="mt-3 text-slate-200 leading-relaxed">{{ $show->overview }}</p>
                    </section>
                @endif

                <section class="rounded-lg bg-slate-900/60 p-6 shadow grid gap-4 sm:grid-cols-2">
                    @if (!empty($show->first_air_date))
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-400">First Aired</p>
                            <p class="text-sm font-semibold">{{ date('M j, Y', strtotime($show->first_air_date)) }}</p>
                        </div>
                    @endif
                    @if (!empty($show->last_air_date))
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-400">Last Aired</p>
                            <p class="text-sm font-semibold">{{ date('M j, Y', strtotime($show->last_air_date)) }}</p>
                        </div>
                    @endif
                    @if (!empty($show->number_of_seasons))
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-400">Seasons</p>
                            <p class="text-sm font-semibold">{{ $show->number_of_seasons }}</p>
                        </div>
                    @endif
                    @if (!empty($show->number_of_episodes))
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-400">Episodes</p>
                            <p class="text-sm font-semibold">{{ $show->number_of_episodes }}</p>
                        </div>
                    @endif
                </section>
            </div>
        </main>
    </div>
    @livewireScripts
</body>
</html>
