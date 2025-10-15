<section class="space-y-10">
    <div class="grid gap-10 lg:grid-cols-[360px,1fr]">
        <div class="overflow-hidden rounded-3xl border border-slate-800 bg-slate-900/80 shadow-lg shadow-slate-950/40">
            <img src="{{ $movie['poster'] }}" alt="{{ $movie['title'] }} poster" class="w-full object-cover">
        </div>
        <div class="space-y-6">
            <div class="space-y-2">
                <p class="text-sm uppercase tracking-widest text-emerald-400">Feature film</p>
                <h1 class="text-4xl font-bold text-slate-50">{{ $movie['title'] }}</h1>
                <p class="text-lg text-slate-300">{{ $movie['tagline'] }}</p>

                <div class="pt-2">
                    @if ($movieId)
                        <livewire:watchlist :movie-id="$movieId" :key="'movie-watchlist-' . $movieId" />
                    @else
                        <span class="inline-flex items-center rounded-full bg-slate-900/60 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-slate-400">
                            {{ __('Watchlist available when this title is synced.') }}
                        </span>
                    @endif
                </div>
            </div>

            <div class="flex flex-wrap gap-4 text-sm text-slate-300">
                <span class="inline-flex items-center gap-2 rounded-full border border-slate-700 px-3 py-1">
                    <svg class="h-4 w-4 text-emerald-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path fill-rule="evenodd" d="M12 2a9 9 0 0 1 9 9c0 4.97-4.03 9-9 9a9 9 0 0 1-9-9c0-4.97 4.03-9 9-9Zm1.5 4.5a1.5 1.5 0 1 0-3 0v3.227l-2.018 2.017a1.5 1.5 0 1 0 2.122 2.122l2.561-2.561A1.5 1.5 0 0 0 13.5 10.5V6.5Z" clip-rule="evenodd" />
                    </svg>
                    {{ $movie['runtime'] }} min
                </span>
                <span class="rounded-full border border-slate-700 px-3 py-1">Released {{ \Carbon\Carbon::parse($movie['release_date'])->format('F j, Y') }}</span>
                <span class="rounded-full border border-slate-700 px-3 py-1">Rating {{ number_format($movie['rating'], 1) }}/10</span>
            </div>

            <p class="text-base leading-relaxed text-slate-300">{{ $movie['overview'] }}</p>

            <dl class="grid gap-6 sm:grid-cols-2">
                <div class="space-y-2 rounded-2xl border border-slate-800 bg-slate-900/60 p-5">
                    <dt class="text-xs uppercase tracking-wide text-slate-400">Genres</dt>
                    <dd class="text-sm text-slate-200">{{ implode(', ', $movie['genres']) }}</dd>
                </div>
                <div class="space-y-2 rounded-2xl border border-slate-800 bg-slate-900/60 p-5">
                    <dt class="text-xs uppercase tracking-wide text-slate-400">Spoken languages</dt>
                    <dd class="text-sm text-slate-200">{{ implode(', ', $movie['languages']) }}</dd>
                </div>
                <div class="space-y-2 rounded-2xl border border-slate-800 bg-slate-900/60 p-5">
                    <dt class="text-xs uppercase tracking-wide text-slate-400">Production countries</dt>
                    <dd class="text-sm text-slate-200">{{ implode(', ', $movie['countries']) }}</dd>
                </div>
                <div class="space-y-2 rounded-2xl border border-slate-800 bg-slate-900/60 p-5">
                    <dt class="text-xs uppercase tracking-wide text-slate-400">Director</dt>
                    <dd class="text-sm text-slate-200">{{ $movie['director'] }}</dd>
                </div>
            </dl>

            <div class="rounded-2xl border border-slate-800 bg-slate-900/60 p-5">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-400">Top billed cast</h2>
                <ul class="mt-3 grid gap-3 sm:grid-cols-2">
                    @foreach ($movie['cast'] as $actor)
                        <li class="rounded-xl border border-slate-800 bg-slate-950/80 px-4 py-2 text-sm text-slate-200">
                            {{ $actor }}
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <div class="space-y-3 rounded-2xl border border-slate-800 bg-slate-900/60 p-5">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-400">Where to stream</h3>
                    <ul class="space-y-2 text-sm text-slate-200">
                        @foreach ($movie['streaming'] as $provider)
                            <li class="flex items-center justify-between rounded-xl border border-slate-800 bg-slate-950/80 px-4 py-2">
                                <span>{{ $provider['provider'] }}</span>
                                <span class="text-xs uppercase text-emerald-300">{{ $provider['quality'] }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="space-y-3 rounded-2xl border border-slate-800 bg-slate-900/60 p-5">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-400">Official trailer</h3>
                    <div class="aspect-video overflow-hidden rounded-xl border border-slate-800 bg-black">
                        <iframe src="{{ $movie['trailer_url'] }}" title="{{ $movie['title'] }} trailer" allowfullscreen class="h-full w-full"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
