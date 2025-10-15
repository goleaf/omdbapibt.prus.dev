<div class="min-h-screen bg-slate-950 text-slate-100">
    <div class="max-w-6xl mx-auto px-4 py-12 space-y-12">
        <header class="space-y-4">
            <div class="flex flex-wrap items-center gap-3 text-sm text-slate-400">
                <span class="inline-flex items-center rounded-full bg-slate-800 px-3 py-1 font-semibold uppercase tracking-wider">
                    {{ strtoupper($locale) }}
                </span>
                @if (! empty($show['genres']))
                    <span>{{ implode(' • ', $show['genres']) }}</span>
                @endif
                @if (! empty($show['first_air_date']))
                    <span>{{ __('First aired') }}: {{ $show['first_air_date'] }}</span>
                @endif
                @if (! empty($show['runtime']))
                    <span>{{ __('Average runtime') }}: {{ $show['runtime'] }} {{ __('minutes') }}</span>
                @endif
                <span>{{ __('Seasons') }}: {{ $show['number_of_seasons'] ?? '—' }}</span>
                <span>{{ __('Episodes') }}: {{ $show['number_of_episodes'] ?? '—' }}</span>
            </div>

            <div>
                <h1 class="text-4xl font-bold tracking-tight">
                    {{ $this->translate($show['name'] ?? null) }}
                </h1>
                @if (! empty($show['tagline']))
                    <p class="mt-2 text-xl text-slate-300">
                        {{ $this->translate($show['tagline']) }}
                    </p>
                @endif

                <div class="pt-3">
                    @if ($tvShowId)
                        <livewire:watchlist :tv-show-id="$tvShowId" :key="'tv-watchlist-' . $tvShowId" />
                    @else
                        <span class="inline-flex items-center rounded-full bg-slate-900/60 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-slate-400">
                            {{ __('Watchlist available when this series is synced.') }}
                        </span>
                    @endif
                </div>
            </div>

            @if (! empty($show['overview']))
                <p class="max-w-3xl text-lg text-slate-300">
                    {{ $this->translate($show['overview']) }}
                </p>
            @endif
        </header>

        <section class="space-y-6">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-semibold">{{ __('Seasons & Episodes') }}</h2>
            </div>

            <div class="space-y-4">
                @forelse ($seasons as $season)
                    <details class="group rounded-xl border border-slate-800 bg-slate-900/60 p-6" @if ($loop->first) open @endif>
                        <summary class="cursor-pointer list-none font-semibold text-lg text-slate-100 flex items-center justify-between gap-4">
                            <span>
                                {{ $this->translate($season['name'] ?? null) ?? __('Season #:number', ['number' => $season['season_number'] ?? '?']) }}
                            </span>
                            <span class="text-sm font-medium text-slate-400">
                                {{ __('Episodes') }}: {{ $season['episode_count'] ?? count($season['episodes'] ?? []) }}
                            </span>
                        </summary>

                        @if (! empty($season['overview']))
                            <p class="mt-4 text-slate-300">
                                {{ $this->translate($season['overview']) }}
                            </p>
                        @endif

                        <div class="mt-6 space-y-3">
                            @foreach ($season['episodes'] ?? [] as $episode)
                                <details class="rounded-lg border border-slate-800/80 bg-slate-950/60 p-4">
                                    <summary class="cursor-pointer list-none text-slate-200 flex flex-wrap items-center justify-between gap-3">
                                        <div>
                                            <span class="font-semibold">
                                                {{ __('Episode #:number', ['number' => $episode['episode_number'] ?? '?']) }}
                                            </span>
                                            <span class="ml-2 text-slate-300">
                                                {{ $this->translate($episode['name'] ?? null) }}
                                            </span>
                                        </div>
                                        <div class="text-xs uppercase tracking-wide text-slate-400">
                                            @if (! empty($episode['air_date']))
                                                <span class="mr-3">{{ __('Air date') }}: {{ $episode['air_date'] }}</span>
                                            @endif
                                            @if (! empty($episode['runtime']))
                                                <span>{{ __('Runtime') }}: {{ $episode['runtime'] }} {{ __('min') }}</span>
                                            @endif
                                        </div>
                                    </summary>
                                    @if (! empty($episode['overview']))
                                        <p class="mt-3 text-sm text-slate-300">
                                            {{ $this->translate($episode['overview']) }}
                                        </p>
                                    @endif
                                </details>
                            @endforeach
                        </div>
                    </details>
                @empty
                    <p class="text-slate-300">{{ __('No season information available for this series.') }}</p>
                @endforelse
            </div>
        </section>

        <section class="space-y-6">
            <h2 class="text-2xl font-semibold">{{ __('Credits') }}</h2>
            <div class="grid gap-8 md:grid-cols-2">
                <div class="space-y-4">
                    <h3 class="text-xl font-semibold text-slate-200">{{ __('Cast') }}</h3>
                    <ul class="space-y-3">
                        @forelse ($credits['cast'] as $member)
                            <li class="rounded-lg border border-slate-800 bg-slate-900/60 p-4">
                                <p class="font-semibold text-slate-100">{{ $member['name'] ?? __('Unknown') }}</p>
                                @if (! empty($member['character']))
                                    <p class="text-sm text-slate-300">
                                        {{ __('as') }} {{ $this->translate($member['character']) }}
                                    </p>
                                @endif
                            </li>
                        @empty
                            <li class="text-sm text-slate-300">{{ __('No cast information available.') }}</li>
                        @endforelse
                    </ul>
                </div>
                <div class="space-y-4">
                    <h3 class="text-xl font-semibold text-slate-200">{{ __('Crew') }}</h3>
                    <ul class="space-y-3">
                        @forelse ($credits['crew'] as $member)
                            <li class="rounded-lg border border-slate-800 bg-slate-900/60 p-4">
                                <p class="font-semibold text-slate-100">{{ $member['name'] ?? __('Unknown') }}</p>
                                @if (! empty($member['job']))
                                    <p class="text-sm text-slate-300">
                                        {{ $this->translate($member['job']) }}
                                    </p>
                                @endif
                            </li>
                        @empty
                            <li class="text-sm text-slate-300">{{ __('No crew information available.') }}</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </section>
    </div>
</div>
