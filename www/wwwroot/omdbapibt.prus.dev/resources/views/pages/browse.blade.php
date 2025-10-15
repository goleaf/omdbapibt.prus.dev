@extends('layouts.app', [
    'title' => 'Browse the catalog',
    'header' => 'Browse trending movies and shows',
    'subheader' => 'Use filters, curated categories, and real-time streaming availability to zero in on what to watch next.',
])

@section('content')
    <div class="grid gap-10 lg:grid-cols-[320px,1fr]">
        <aside class="space-y-6">
            <div class="rounded-3xl border border-slate-800 bg-slate-900/70 p-6">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-400">Filters</h2>
                @livewire('media-filters')
            </div>
            <div class="rounded-3xl border border-slate-800 bg-slate-900/70 p-6">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-400">Curated hubs</h3>
                <ul class="mt-3 space-y-2 text-sm text-slate-300">
                    <li class="rounded-2xl border border-slate-800 bg-slate-950/80 px-4 py-2">Award winners</li>
                    <li class="rounded-2xl border border-slate-800 bg-slate-950/80 px-4 py-2">Critics choice</li>
                    <li class="rounded-2xl border border-slate-800 bg-slate-950/80 px-4 py-2">Coming soon</li>
                </ul>
            </div>
        </aside>

        <section class="space-y-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-2xl font-semibold text-slate-50">Trending right now</h2>
                    <p class="text-sm text-slate-400">Updated hourly based on TMDb popularity and OMDb ratings.</p>
                </div>
                <div class="flex gap-3">
                    <button class="rounded-full border border-slate-700 px-4 py-2 text-sm text-slate-200 transition hover:border-emerald-400 hover:text-emerald-200">Save filters</button>
                    <button class="rounded-full bg-emerald-500 px-4 py-2 text-sm font-semibold text-emerald-950 transition hover:bg-emerald-400">Randomize</button>
                </div>
            </div>

            @livewire('trending-reel')

            <div class="space-y-6">
                <h3 class="text-xl font-semibold text-slate-50">More to explore</h3>
                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach (['Neo Noir', 'Time Travel', 'Global Cinema', 'Documentary Spotlight', 'Family Favorites', 'Hidden Gems'] as $collection)
                        <a href="#" class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5 text-sm text-slate-200 transition hover:border-emerald-400/60">
                            <span class="font-semibold text-slate-50">{{ $collection }}</span>
                            <p class="mt-2 text-xs text-slate-400">Dive into our hand-picked {{ strtolower($collection) }} selections.</p>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    </div>
@endsection
