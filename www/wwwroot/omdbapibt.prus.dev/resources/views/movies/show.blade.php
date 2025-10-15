<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $movie->title ?? 'Movie Details' }} &mdash; {{ config('app.name', 'Laravel') }}</title>
    @vite('resources/css/app.css')
    @livewireStyles
</head>
<body class="antialiased bg-slate-950 text-slate-100">
    <div class="min-h-screen">
        <header class="bg-slate-900/70 backdrop-blur">
            <div class="mx-auto max-w-6xl px-6 py-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-3xl font-semibold">{{ $movie->title ?? 'Unknown Title' }}</h1>
                    @if (!empty($movie->tagline))
                        <p class="text-slate-300">{{ $movie->tagline }}</p>
                    @endif
                </div>
                <div class="flex items-center gap-4">
                    <span class="rounded-full bg-slate-800 px-3 py-1 text-xs uppercase tracking-wider text-slate-300">Movie</span>
                    <livewire:watchlist watchable-type="movie" :watchable-id="$movie->id" :show-list="false" />
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-6xl px-6 py-10 grid gap-8 md:grid-cols-[240px,1fr]">
            @if (!empty($movie->poster_path))
                <div>
                    <img
                        src="{{ $movie->poster_path }}"
                        alt="{{ $movie->title }} poster"
                        class="w-full rounded-lg shadow-lg"
                    >
                </div>
            @endif

            <div class="space-y-6">
                @if (!empty($movie->plot))
                    <section class="rounded-lg bg-slate-900/60 p-6 shadow">
                        <h2 class="text-lg font-semibold text-white">Overview</h2>
                        <p class="mt-3 text-slate-200 leading-relaxed">{{ $movie->plot }}</p>
                    </section>
                @endif

                <section class="rounded-lg bg-slate-900/60 p-6 shadow grid gap-4 sm:grid-cols-2">
                    @if (!empty($movie->release_date))
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-400">Release Date</p>
                            <p class="text-sm font-semibold">{{ date('M j, Y', strtotime($movie->release_date)) }}</p>
                        </div>
                    @endif
                    @if (!empty($movie->runtime))
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-400">Runtime</p>
                            <p class="text-sm font-semibold">{{ $movie->runtime }} minutes</p>
                        </div>
                    @endif
                    @if (!empty($movie->vote_average))
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-400">Rating</p>
                            <p class="text-sm font-semibold">{{ number_format($movie->vote_average, 1) }} / 10</p>
                        </div>
                    @endif
                    @if (!empty($movie->vote_count))
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-400">Votes</p>
                            <p class="text-sm font-semibold">{{ number_format($movie->vote_count) }}</p>
                        </div>
                    @endif
                </section>
            </div>
        </main>
    </div>
    @livewireScripts
</body>
</html>
