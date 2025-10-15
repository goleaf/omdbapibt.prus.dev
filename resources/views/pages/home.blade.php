@extends('layouts.app', [
    'title' => 'Discover movies and TV shows',
    'header' => 'Cinematic intelligence for superfans',
    'subheader' => 'Deep metadata, real-time streaming availability, and curated collections to keep your watchlist inspired.',
])

@section('content')
    <section class="grid gap-10 lg:grid-cols-[1.1fr,0.9fr]">
        <div class="space-y-6">
            <div class="space-y-4">
                <p class="text-sm uppercase tracking-[0.35em] text-emerald-400">Now featuring Flux UI</p>
                <h2 class="text-4xl font-bold text-slate-50 sm:text-5xl">Your personal portal to the movie multiverse</h2>
                <p class="text-lg text-slate-300">
                    Search over 1M+ titles across OMDb and TMDb, follow cast members, and receive instant alerts when your
                    favorites hit streaming. Built entirely with Livewire for a fluid, reactive experience.
                </p>
            </div>
            <div class="flex flex-col gap-4 sm:flex-row">
                <a href="{{ route('browse') }}" class="rounded-full bg-emerald-500 px-6 py-3 text-center text-base font-semibold text-emerald-950 transition hover:bg-emerald-400">Start browsing</a>
                <a href="{{ route('pricing') }}" class="rounded-full border border-slate-700 px-6 py-3 text-center text-base font-semibold text-slate-200 transition hover:border-emerald-400 hover:text-emerald-200">See plans</a>
            </div>
            <dl class="grid gap-4 sm:grid-cols-3">
                <div class="rounded-2xl border border-slate-800 bg-slate-900/60 p-4">
                    <dt class="text-xs uppercase tracking-wide text-slate-400">Curated collections</dt>
                    <dd class="mt-2 text-2xl font-semibold text-slate-50">400+</dd>
                </div>
                <div class="rounded-2xl border border-slate-800 bg-slate-900/60 p-4">
                    <dt class="text-xs uppercase tracking-wide text-slate-400">Daily updates</dt>
                    <dd class="mt-2 text-2xl font-semibold text-slate-50">5K+</dd>
                </div>
                <div class="rounded-2xl border border-slate-800 bg-slate-900/60 p-4">
                    <dt class="text-xs uppercase tracking-wide text-slate-400">Global languages</dt>
                    <dd class="mt-2 text-2xl font-semibold text-slate-50">35</dd>
                </div>
            </dl>
        </div>

        <div class="rounded-3xl border border-slate-800 bg-slate-900/70 p-6">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-400">Quick filters</h3>
            @livewire('media-filters')
        </div>
    </section>

    <div class="mt-16 space-y-16">
        @livewire('trending-reel')

        <section class="grid gap-10 lg:grid-cols-2">
            <div class="space-y-3">
                <h2 class="text-2xl font-semibold text-slate-50">Livewire-first detail pages</h2>
                <p class="text-base text-slate-300">Every movie and series page renders comprehensive metadata instantly — cast, crew, streaming partners, and trailers delivered without page reloads.</p>
                <div class="flex flex-wrap gap-3 text-sm text-slate-300">
                    <span class="rounded-full border border-slate-700 px-3 py-1">Dynamic credits</span>
                    <span class="rounded-full border border-slate-700 px-3 py-1">Streaming availability</span>
                    <span class="rounded-full border border-slate-700 px-3 py-1">Trailer embeds</span>
                    <span class="rounded-full border border-slate-700 px-3 py-1">Watchlists</span>
                </div>
            </div>
            <div class="rounded-3xl border border-slate-800 bg-slate-900/60 p-6">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-400">Sneak peek</h3>
                <p class="text-sm text-slate-300">Visit a movie or show page to see the full interactive layout.</p>
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <a href="{{ route('movies.show', 'the-galactic-voyage') }}" class="rounded-2xl border border-slate-800 bg-slate-950/80 p-4 text-sm text-slate-200 transition hover:border-emerald-400">Explore movie layout</a>
                    <a href="{{ route('shows.show', 'eclipse-station') }}" class="rounded-2xl border border-slate-800 bg-slate-950/80 p-4 text-sm text-slate-200 transition hover:border-emerald-400">Explore series layout</a>
                </div>
            </div>
        </section>
    </div>
@endsection
