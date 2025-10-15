<section class="space-y-10">
    <div class="grid gap-10 lg:grid-cols-[360px,1fr]">
        <div class="overflow-hidden rounded-3xl border border-slate-800 bg-slate-900/80 shadow-lg shadow-slate-950/40">
            <img src="{{ $show['poster'] }}" alt="{{ $show['name'] }} poster" class="w-full object-cover">
        </div>
        <div class="space-y-6">
            <div class="space-y-2">
                <p class="text-sm uppercase tracking-widest text-emerald-400">Original series</p>
                <h1 class="text-4xl font-bold text-slate-50">{{ $show['name'] }}</h1>
                <p class="text-lg text-slate-300">{{ $show['tagline'] }}</p>
            </div>

            <div class="flex flex-wrap gap-4 text-sm text-slate-300">
                <span class="inline-flex items-center gap-2 rounded-full border border-slate-700 px-3 py-1">
                    <svg class="h-4 w-4 text-emerald-400" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M4 5.5A2.5 2.5 0 0 1 6.5 3h11A2.5 2.5 0 0 1 20 5.5v13a.5.5 0 0 1-.76.43l-4.9-3.024a2.5 2.5 0 0 0-2.62-.006L6.72 18.93a.5.5 0 0 1-.72-.43Z" />
                    </svg>
                    {{ $show['episode_run_time'] }} min episodes
                </span>
                <span class="rounded-full border border-slate-700 px-3 py-1">{{ $show['seasons'] }} seasons · {{ $show['episodes'] }} episodes</span>
                <span class="rounded-full border border-slate-700 px-3 py-1">Aired {{ \Carbon\Carbon::parse($show['first_air_date'])->format('M Y') }} – {{ \Carbon\Carbon::parse($show['last_air_date'])->format('M Y') }}</span>
                <span class="rounded-full border border-slate-700 px-3 py-1">Rating {{ number_format($show['rating'], 1) }}/10</span>
            </div>

            <p class="text-base leading-relaxed text-slate-300">{{ $show['overview'] }}</p>

            <dl class="grid gap-6 sm:grid-cols-2">
                <div class="space-y-2 rounded-2xl border border-slate-800 bg-slate-900/60 p-5">
                    <dt class="text-xs uppercase tracking-wide text-slate-400">Genres</dt>
                    <dd class="text-sm text-slate-200">{{ implode(', ', $show['genres']) }}</dd>
                </div>
                <div class="space-y-2 rounded-2xl border border-slate-800 bg-slate-900/60 p-5">
                    <dt class="text-xs uppercase tracking-wide text-slate-400">Languages</dt>
                    <dd class="text-sm text-slate-200">{{ implode(', ', $show['languages']) }}</dd>
                </div>
                <div class="space-y-2 rounded-2xl border border-slate-800 bg-slate-900/60 p-5">
                    <dt class="text-xs uppercase tracking-wide text-slate-400">Creators</dt>
                    <dd class="text-sm text-slate-200">{{ implode(', ', $show['creators']) }}</dd>
                </div>
            </dl>

            <div class="rounded-2xl border border-slate-800 bg-slate-900/60 p-5">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-400">Main cast</h2>
                <ul class="mt-3 grid gap-3 sm:grid-cols-2">
                    @foreach ($show['cast'] as $actor)
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
                        @foreach ($show['streaming'] as $provider)
                            <li class="flex items-center justify-between rounded-xl border border-slate-800 bg-slate-950/80 px-4 py-2">
                                <span>{{ $provider['provider'] }}</span>
                                <span class="text-xs uppercase text-emerald-300">{{ $provider['quality'] }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="space-y-3 rounded-2xl border border-slate-800 bg-slate-900/60 p-5">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-400">Season trailer</h3>
                    <div class="aspect-video overflow-hidden rounded-xl border border-slate-800 bg-black">
                        <iframe src="{{ $show['trailer_url'] }}" title="{{ $show['name'] }} trailer" allowfullscreen class="h-full w-full"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
