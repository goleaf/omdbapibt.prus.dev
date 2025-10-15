<div class="space-y-12">
    <section class="grid gap-8 lg:grid-cols-[280px,1fr]">
        <div class="overflow-hidden rounded-3xl border border-slate-800 bg-slate-900/60 shadow-lg">
            @if ($movie->poster_path)
                <img
                    src="{{ $movie->poster_path }}"
                    alt="{{ __('Poster for :title', ['title' => $localizedTitle]) }}"
                    class="h-full w-full object-cover"
                />
            @else
                <div class="flex h-full min-h-[360px] items-center justify-center bg-slate-900/70 text-sm text-slate-400">
                    {{ __('Poster not available') }}
                </div>
            @endif
        </div>

        <div class="space-y-6">
            <div class="space-y-3">
                <div class="flex flex-wrap items-center gap-3 text-xs uppercase tracking-[0.25em] text-emerald-400">
                    <span class="rounded-full border border-emerald-500/40 px-3 py-1">
                        {{ strtoupper($movie->media_type ?? 'movie') }}
                    </span>
                    @if ($movie->release_date)
                        <span class="rounded-full border border-slate-700 px-3 py-1 text-slate-300">
                            {{ $movie->release_date->format('F j, Y') }}
                        </span>
                    @endif
                    @if (! is_null($movie->runtime))
                        <span class="rounded-full border border-slate-700 px-3 py-1 text-slate-300">
                            {{ $movie->runtime }} {{ __('min') }}
                        </span>
                    @endif
                    @if (! is_null($movie->vote_average))
                        <span class="rounded-full border border-slate-700 px-3 py-1 text-slate-300">
                            {{ __('Rating') }} {{ number_format($movie->vote_average, 1) }}/10
                        </span>
                    @endif
                </div>

                <div>
                    <h1 class="text-4xl font-bold tracking-tight text-slate-50 sm:text-5xl">
                        {{ $localizedTitle }}
                    </h1>
                    @if ($movie->tagline)
                        <p class="mt-2 text-lg text-slate-300">
                            {{ $movie->tagline }}
                        </p>
                    @endif
                </div>
            </div>

            @if ($movie->overview)
                <p class="text-lg text-slate-300">
                    {{ $this->translate($movie->overview) }}
                </p>
            @endif

            <dl class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 text-sm text-slate-300">
                <div>
                    <dt class="font-semibold text-slate-200">{{ __('Status') }}</dt>
                    <dd class="mt-1">{{ $movie->status ?? __('Unknown') }}</dd>
                </div>
                <div>
                    <dt class="font-semibold text-slate-200">{{ __('Budget') }}</dt>
                    <dd class="mt-1">
                        @if (! is_null($movie->budget))
                            ${{ number_format((int) $movie->budget) }}
                        @else
                            {{ __('Not reported') }}
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="font-semibold text-slate-200">{{ __('Revenue') }}</dt>
                    <dd class="mt-1">
                        @if (! is_null($movie->revenue))
                            ${{ number_format((int) $movie->revenue) }}
                        @else
                            {{ __('Not reported') }}
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="font-semibold text-slate-200">{{ __('Genres') }}</dt>
                    <dd class="mt-1">
                        {{ collect($genreNames)->filter()->implode(' • ') ?: __('Not categorized yet') }}
                    </dd>
                </div>
                <div>
                    <dt class="font-semibold text-slate-200">{{ __('Languages') }}</dt>
                    <dd class="mt-1">
                        {{ collect($languageNames)->filter()->implode(' • ') ?: __('No languages listed') }}
                    </dd>
                </div>
                <div>
                    <dt class="font-semibold text-slate-200">{{ __('Countries') }}</dt>
                    <dd class="mt-1">
                        {{ collect($countryNames)->filter()->implode(' • ') ?: __('No countries listed') }}
                    </dd>
                </div>
            </dl>
        </div>
    </section>

    <section class="space-y-6">
        <flux:navlist class="flex flex-wrap items-center gap-2 border-b border-slate-800 pb-3" variant="outline">
            @foreach ($tabLabels as $key => $label)
                <flux:navlist.item
                    wire:key="tab-{{ $key }}"
                    class="w-auto px-4 py-2 text-sm font-semibold uppercase tracking-wide"
                    :data-current="$activeTab === $key"
                    wire:click="setTab('{{ $key }}')"
                >
                    {{ __($label) }}
                </flux:navlist.item>
            @endforeach
        </flux:navlist>

        <div class="rounded-3xl border border-slate-800 bg-slate-900/60 p-6 sm:p-8">
            @if ($activeTab === 'overview')
                <div class="space-y-8">
                    <div>
                        <h2 class="text-xl font-semibold text-slate-100">{{ __('Story overview') }}</h2>
                        <p class="mt-3 text-base leading-relaxed text-slate-300">
                            {{ $this->translate($movie->overview) ?? __('We are still gathering a synopsis for this title.') }}
                        </p>
                    </div>

                    <div class="grid gap-6 lg:grid-cols-2">
                        <div class="rounded-2xl border border-slate-800 bg-slate-950/60 p-5">
                            <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-400">{{ __('Key facts') }}</h3>
                            <dl class="mt-4 space-y-3 text-sm text-slate-300">
                                <div class="flex items-start justify-between gap-4">
                                    <dt class="font-medium text-slate-200">{{ __('Release date') }}</dt>
                                    <dd>
                                        {{ $movie->release_date ? $movie->release_date->translatedFormat('F j, Y') : __('TBD') }}
                                    </dd>
                                </div>
                                <div class="flex items-start justify-between gap-4">
                                    <dt class="font-medium text-slate-200">{{ __('Runtime') }}</dt>
                                    <dd>{{ ! is_null($movie->runtime) ? $movie->runtime . ' ' . __('minutes') : __('Unknown') }}</dd>
                                </div>
                                <div class="flex items-start justify-between gap-4">
                                    <dt class="font-medium text-slate-200">{{ __('Popularity') }}</dt>
                                    <dd>{{ ! is_null($movie->popularity) ? number_format($movie->popularity, 1) : __('Not tracked') }}</dd>
                                </div>
                                <div class="flex items-start justify-between gap-4">
                                    <dt class="font-medium text-slate-200">{{ __('Vote count') }}</dt>
                                    <dd>{{ ! is_null($movie->vote_count) ? number_format($movie->vote_count) : __('No votes yet') }}</dd>
                                </div>
                            </dl>
                        </div>

                        <div class="rounded-2xl border border-slate-800 bg-slate-950/60 p-5">
                            <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-400">{{ __('Top billed cast') }}</h3>
                            <ul class="mt-4 space-y-3">
                                @foreach (collect($movie->cast ?? [])->sortBy('order')->values()->take(5) as $index => $member)
                                    <li class="flex items-center justify-between gap-4 rounded-lg border border-slate-800/60 bg-slate-900/80 px-4 py-3" wire:key="cast-overview-{{ $index }}">
                                        <div>
                                            <p class="font-semibold text-slate-100">{{ $member['name'] ?? __('Unknown') }}</p>
                                            @if (! empty($member['character']))
                                                <p class="text-xs uppercase tracking-wide text-slate-400">{{ __('as') }} {{ $member['character'] }}</p>
                                            @endif
                                        </div>
                                        @if (! empty($member['order']))
                                            <span class="text-xs font-semibold text-slate-400">#{{ $member['order'] }}</span>
                                        @endif
                                    </li>
                                @endforeach
                                @if (empty($movie->cast))
                                    <li class="rounded-lg border border-dashed border-slate-700 px-4 py-3 text-sm text-slate-400">
                                        {{ __('Cast information is on the way.') }}
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            @elseif ($activeTab === 'credits')
                <div class="grid gap-8 lg:grid-cols-2">
                    <div class="space-y-4">
                        <h2 class="text-lg font-semibold text-slate-100">{{ __('Cast') }}</h2>
                        <ul class="space-y-3">
                            @forelse (collect($movie->cast ?? [])->sortBy('order')->values() as $index => $member)
                                <li class="rounded-xl border border-slate-800 bg-slate-950/70 p-4" wire:key="cast-{{ $index }}">
                                    <p class="font-semibold text-slate-100">{{ $member['name'] ?? __('Unknown performer') }}</p>
                                    @if (! empty($member['character']))
                                        <p class="text-sm text-slate-300">{{ __('as') }} {{ $member['character'] }}</p>
                                    @endif
                                </li>
                            @empty
                                <li class="text-sm text-slate-300">{{ __('We have not indexed the cast for this title yet.') }}</li>
                            @endforelse
                        </ul>
                    </div>
                    <div class="space-y-4">
                        <h2 class="text-lg font-semibold text-slate-100">{{ __('Crew') }}</h2>
                        <ul class="space-y-3">
                            @forelse (collect($movie->crew ?? [])->values() as $index => $member)
                                <li class="rounded-xl border border-slate-800 bg-slate-950/70 p-4" wire:key="crew-{{ $index }}">
                                    <p class="font-semibold text-slate-100">{{ $member['name'] ?? __('Unknown crew member') }}</p>
                                    @if (! empty($member['job']))
                                        <p class="text-sm text-slate-300">{{ $member['job'] }}</p>
                                    @endif
                                </li>
                            @empty
                                <li class="text-sm text-slate-300">{{ __('Crew details are coming soon.') }}</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            @elseif ($activeTab === 'streaming')
                @php($streamingLinks = collect($movie->streaming_links ?? []))
                @if ($streamingLinks->isEmpty())
                    <p class="text-sm text-slate-300">{{ __('We are tracking streaming availability now. Check back shortly.') }}</p>
                @else
                    <div class="space-y-4">
                        <h2 class="text-lg font-semibold text-slate-100">{{ __('Where to watch') }}</h2>
                        <div class="space-y-3">
                            @foreach ($streamingLinks as $index => $link)
                                <div class="flex flex-wrap items-center justify-between gap-4 rounded-xl border border-slate-800 bg-slate-950/70 px-4 py-4" wire:key="stream-{{ $index }}">
                                    <div>
                                        <p class="text-base font-semibold text-slate-100">{{ $link['service'] ?? __('Streaming partner') }}</p>
                                        <p class="text-sm text-slate-300">
                                            {{ ! empty($link['type']) ? ucfirst($link['type']) : __('Available') }}
                                            @if (! empty($link['quality']))
                                                • {{ strtoupper($link['quality']) }}
                                            @endif
                                        </p>
                                    </div>
                                    @if (! empty($link['url']))
                                        <a
                                            href="{{ $link['url'] }}"
                                            target="_blank"
                                            rel="noopener"
                                            class="rounded-full border border-emerald-500/70 px-4 py-2 text-sm font-semibold text-emerald-300 transition hover:bg-emerald-500/10"
                                        >
                                            {{ __('Open') }}
                                        </a>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @elseif ($activeTab === 'trailers')
                @php($trailers = collect($movie->trailers ?? []))
                @if ($trailers->isEmpty())
                    <p class="text-sm text-slate-300">{{ __('No trailers have been linked yet.') }}</p>
                @else
                    @php($primary = $trailers->first())
                    <div class="space-y-6">
                        @if ($primary)
                            @php($embedUrl = $this->embedUrlFor($primary))
                            <div class="aspect-video overflow-hidden rounded-2xl border border-slate-800 bg-black">
                                @if ($embedUrl)
                                    <iframe
                                        src="{{ $embedUrl }}"
                                        title="{{ $primary['name'] ?? __('Trailer') }}"
                                        frameborder="0"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                        allowfullscreen
                                        class="h-full w-full"
                                    ></iframe>
                                @else
                                    <div class="flex h-full items-center justify-center bg-slate-900/80 text-sm text-slate-300">
                                        {{ __('Unable to render this trailer automatically.') }}
                                    </div>
                                @endif
                            </div>
                        @endif

                        <div class="space-y-3">
                            @foreach ($trailers as $index => $trailer)
                                <div class="rounded-xl border border-slate-800 bg-slate-950/70 px-4 py-3" wire:key="trailer-{{ $index }}">
                                    <div class="flex flex-wrap items-center justify-between gap-4">
                                        <div>
                                            <p class="text-base font-semibold text-slate-100">{{ $trailer['name'] ?? __('Trailer') }}</p>
                                            <p class="text-xs uppercase tracking-wide text-slate-400">{{ $trailer['site'] ?? __('Unknown site') }}</p>
                                        </div>
                                        @if (! empty($trailer['url']))
                                            <a
                                                href="{{ $trailer['url'] }}"
                                                target="_blank"
                                                rel="noopener"
                                                class="rounded-full border border-slate-700 px-3 py-1 text-sm text-slate-300 transition hover:border-emerald-400 hover:text-emerald-200"
                                            >
                                                {{ __('Watch externally') }}
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @elseif ($activeTab === 'reviews')
                @if ($this->reviews->isEmpty())
                    <p class="text-sm text-slate-300">{{ __('No community reviews yet. Be the first to share your thoughts!') }}</p>
                @else
                    <div class="space-y-4">
                        @foreach ($this->reviews as $review)
                            <article class="rounded-2xl border border-slate-800 bg-slate-950/70 p-5" wire:key="review-{{ $review->id }}">
                                <header class="flex items-center justify-between gap-4 text-sm text-slate-300">
                                    <div class="font-semibold text-slate-100">{{ $review->user->name }}</div>
                                    <span class="rounded-full border border-emerald-500/40 px-3 py-1 text-xs font-semibold text-emerald-300">
                                        {{ $review->rating }}/10
                                    </span>
                                </header>
                                <div class="mt-3 text-sm leading-relaxed text-slate-300">
                                    {!! $review->sanitized_body !!}
                                </div>
                                <footer class="mt-3 text-xs uppercase tracking-wide text-slate-500">
                                    {{ $review->created_at->diffForHumans() }}
                                </footer>
                            </article>
                        @endforeach
                    </div>
                @endif
            @elseif ($activeTab === 'translations')
                @php($translations = collect($movie->translations ?? []))
                @if ($translations->isEmpty())
                    <p class="text-sm text-slate-300">{{ __('No alternate translations have been supplied yet.') }}</p>
                @else
                    <div class="grid gap-4 md:grid-cols-2">
                        @foreach ($translations as $locale => $payload)
                            <div class="rounded-2xl border border-slate-800 bg-slate-950/70 p-5" wire:key="translation-{{ $locale }}">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-semibold uppercase tracking-[0.3em] text-emerald-400">{{ strtoupper($locale) }}</span>
                                    @if ($locale === $this->locale)
                                        <span class="text-xs text-emerald-300">{{ __('Current view') }}</span>
                                    @endif
                                </div>
                                <dl class="mt-4 space-y-3 text-sm text-slate-300">
                                    <div>
                                        <dt class="font-semibold text-slate-200">{{ __('Title') }}</dt>
                                        <dd class="mt-1">{{ $payload['title'] ?? __('Unavailable') }}</dd>
                                    </div>
                                    <div>
                                        <dt class="font-semibold text-slate-200">{{ __('Tagline') }}</dt>
                                        <dd class="mt-1">{{ $payload['tagline'] ?? __('Unavailable') }}</dd>
                                    </div>
                                    <div>
                                        <dt class="font-semibold text-slate-200">{{ __('Overview') }}</dt>
                                        <dd class="mt-1 leading-relaxed">{{ $payload['overview'] ?? __('Unavailable') }}</dd>
                                    </div>
                                </dl>
                            </div>
                        @endforeach
                    </div>
                @endif
            @endif
        </div>
    </section>
</div>
