<div class="min-h-screen bg-zinc-950 text-zinc-100">
    <div class="relative isolate overflow-hidden bg-gradient-to-b from-zinc-900 via-zinc-950 to-zinc-950">
        @if ($movie->backdrop_path)
            <img
                src="{{ $movie->backdrop_path }}"
                alt="{{ $movie->translated('title') ?? $movie->title }} backdrop"
                class="absolute inset-0 h-full w-full object-cover opacity-30"
            />
        @endif

        <div class="absolute inset-0 bg-gradient-to-br from-zinc-950/90 via-zinc-950/95 to-zinc-950"></div>

        <div class="relative mx-auto flex max-w-6xl flex-col gap-10 px-6 pb-16 pt-12 lg:flex-row lg:px-12">
            <div class="w-full shrink-0 space-y-6 lg:w-1/3">
                <div class="overflow-hidden rounded-3xl border border-white/10 bg-zinc-900/50 shadow-2xl">
                    @if ($movie->poster_path)
                        <img
                            src="{{ $movie->poster_path }}"
                            alt="{{ $movie->translated('title') ?? $movie->title }} poster"
                            class="h-full w-full object-cover"
                        />
                    @else
                        <div class="flex h-full min-h-[420px] items-center justify-center bg-zinc-900/60 text-sm text-zinc-400">
                            Poster not available
                        </div>
                    @endif
                </div>

                <div class="rounded-2xl border border-white/10 bg-zinc-900/50 p-6 backdrop-blur">
                    <div class="flex items-center justify-between text-sm uppercase tracking-wide text-zinc-400">
                        <span>Rating</span>
                        <span>{{ $movie->rating_percent ? $movie->rating_percent.'%' : 'N/A' }}</span>
                    </div>
                    <div class="mt-4 flex flex-col gap-3 text-sm text-zinc-300">
                        @if ($movie->release_date)
                            <div class="flex justify-between">
                                <span class="text-zinc-500">Release</span>
                                <span>{{ $movie->release_date->translatedFormat('M j, Y') }}</span>
                            </div>
                        @endif

                        @if ($movie->runtime)
                            <div class="flex justify-between">
                                <span class="text-zinc-500">Runtime</span>
                                <span>{{ $movie->runtime }} mins</span>
                            </div>
                        @endif

                        @if ($movie->status)
                            <div class="flex justify-between">
                                <span class="text-zinc-500">Status</span>
                                <span>{{ $movie->status }}</span>
                            </div>
                        @endif

                        @if ($movie->homepage)
                            <div class="flex justify-between">\n                                <span class="text-zinc-500">Website</span>
                                <a href="{{ $movie->homepage }}" target="_blank" rel="noopener" class="text-amber-300 hover:text-amber-200">
                                    Visit site
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="flex-1 space-y-6">
                <div class="space-y-4">
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-3">
                        <h1 class="text-3xl font-semibold tracking-tight md:text-4xl">
                            {{ $movie->translated('title') ?? $movie->title }}
                        </h1>

                        @if ($movie->year)
                            <span class="rounded-full border border-amber-400/20 bg-amber-400/10 px-3 py-1 text-xs font-semibold uppercase tracking-wider text-amber-200">
                                {{ $movie->year }}
                            </span>
                        @endif
                    </div>

                    @if ($movie->tagline || $movie->translated('tagline'))
                        <p class="text-lg italic text-zinc-300">
                            {{ $movie->translated('tagline') ?? $movie->tagline }}
                        </p>
                    @endif

                    <div class="text-sm text-zinc-300">
                        {!! nl2br(e($movie->translated('plot') ?? $movie->plot)) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mx-auto max-w-6xl px-6 py-10 lg:px-12">
        <div class="overflow-x-auto">
            <flux:navlist class="flex flex-row gap-2 border-b border-white/10 pb-2" variant="outline">
                <flux:navlist.item
                    wire:click="setTab('overview')"
                    type="button"
                    :data-current="$activeTab === 'overview' ? true : null"
                >
                    Overview
                </flux:navlist.item>

                <flux:navlist.item
                    wire:click="setTab('cast')"
                    type="button"
                    :data-current="$activeTab === 'cast' ? true : null"
                >
                    Cast
                </flux:navlist.item>

                <flux:navlist.item
                    wire:click="setTab('crew')"
                    type="button"
                    :data-current="$activeTab === 'crew' ? true : null"
                >
                    Crew
                </flux:navlist.item>

                <flux:navlist.item
                    wire:click="setTab('streaming')"
                    type="button"
                    :data-current="$activeTab === 'streaming' ? true : null"
                >
                    Streaming
                </flux:navlist.item>

                <flux:navlist.item
                    wire:click="setTab('trailers')"
                    type="button"
                    :data-current="$activeTab === 'trailers' ? true : null"
                >
                    Trailers
                </flux:navlist.item>

                <flux:navlist.item
                    wire:click="setTab('reviews')"
                    type="button"
                    :data-current="$activeTab === 'reviews' ? true : null"
                >
                    Reviews
                </flux:navlist.item>

                <flux:navlist.item
                    wire:click="setTab('translations')"
                    type="button"
                    :data-current="$activeTab === 'translations' ? true : null"
                >
                    Translations
                </flux:navlist.item>
            </flux:navlist>
        </div>

        <div class="mt-8 space-y-6">
            @if ($activeTab === 'overview')
                <section class="grid gap-8 lg:grid-cols-3">
                    <article class="space-y-4 lg:col-span-2">
                        <h2 class="text-xl font-semibold text-white">Synopsis</h2>
                        <p class="text-sm leading-relaxed text-zinc-300">
                            {!! nl2br(e($movie->translated('plot') ?? $movie->plot)) !!}
                        </p>
                    </article>

                    <aside class="space-y-3 rounded-2xl border border-white/10 bg-zinc-900/50 p-6">
                        <h3 class="text-sm font-semibold uppercase tracking-widest text-zinc-400">Identifiers</h3>
                        <dl class="mt-4 space-y-2 text-sm text-zinc-300">
                            @if ($movie->tmdb_id)
                                <div class="flex justify-between">
                                    <dt class="text-zinc-500">TMDb</dt>
                                    <dd>{{ $movie->tmdb_id }}</dd>
                                </div>
                            @endif
                            @if ($movie->imdb_id)
                                <div class="flex justify-between">
                                    <dt class="text-zinc-500">IMDb</dt>
                                    <dd>{{ $movie->imdb_id }}</dd>
                                </div>
                            @endif
                            @if ($movie->omdb_id)
                                <div class="flex justify-between">
                                    <dt class="text-zinc-500">OMDb</dt>
                                    <dd>{{ $movie->omdb_id }}</dd>
                                </div>
                            @endif
                        </dl>
                    </aside>
                </section>
            @elseif ($activeTab === 'cast')
                <section class="space-y-4">
                    <h2 class="text-xl font-semibold text-white">Featured Cast</h2>
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                        @forelse ($this->castMembers as $member)
                            <div class="flex items-center gap-4 rounded-2xl border border-white/10 bg-zinc-900/40 p-4">
                                <div class="flex h-12 w-12 items-center justify-center overflow-hidden rounded-full bg-zinc-800 text-sm font-semibold">
                                    {{ Str::of($member['name'])->substr(0, 2)->upper() }}
                                </div>
                                <div>
                                    <p class="font-medium text-white">{{ $member['name'] }}</p>
                                    @if (! empty($member['character']))
                                        <p class="text-xs text-zinc-400">as {{ $member['character'] }}</p>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-zinc-400">Cast information has not been added yet.</p>
                        @endforelse
                    </div>
                </section>
            @elseif ($activeTab === 'crew')
                <section class="space-y-4">
                    <h2 class="text-xl font-semibold text-white">Production Crew</h2>
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                        @forelse ($this->crewMembers as $member)
                            <div class="flex flex-col gap-1 rounded-2xl border border-white/10 bg-zinc-900/40 p-4">
                                <p class="font-medium text-white">{{ $member['name'] }}</p>
                                @if (! empty($member['job']))
                                    <p class="text-xs uppercase tracking-wider text-amber-300">{{ $member['job'] }}</p>
                                @elseif (! empty($member['department']))
                                    <p class="text-xs uppercase tracking-wider text-amber-300">{{ $member['department'] }}</p>
                                @endif
                            </div>
                        @empty
                            <p class="text-sm text-zinc-400">Crew information has not been added yet.</p>
                        @endforelse
                    </div>
                </section>
            @elseif ($activeTab === 'streaming')
                <section class="space-y-4">
                    <h2 class="text-xl font-semibold text-white">Where to Watch</h2>
                    <div class="grid gap-4 md:grid-cols-2">
                        @forelse ($this->streamingLinks as $link)
                            <div class="flex flex-col gap-2 rounded-2xl border border-white/10 bg-zinc-900/40 p-5">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="font-medium text-white">{{ data_get($link, 'provider') ?? 'Streaming provider' }}</span>
                                    @if ($plan = data_get($link, 'type'))
                                        <span class="rounded-full bg-amber-400/10 px-3 py-1 text-xs font-semibold uppercase tracking-widest text-amber-200">{{ $plan }}</span>
                                    @endif
                                </div>
                                @if ($url = data_get($link, 'url'))
                                    <a href="{{ $url }}" target="_blank" rel="noopener" class="text-sm text-amber-300 hover:text-amber-200">
                                        Open provider page
                                    </a>
                                @endif
                                @if ($price = data_get($link, 'price'))
                                    <p class="text-xs text-zinc-400">{{ $price }}</p>
                                @endif
                            </div>
                        @empty
                            <p class="text-sm text-zinc-400">No streaming providers have been linked yet.</p>
                        @endforelse
                    </div>
                </section>
            @elseif ($activeTab === 'trailers')
                <section class="space-y-4">
                    <h2 class="text-xl font-semibold text-white">Trailers &amp; Videos</h2>
                    <div class="grid gap-6 lg:grid-cols-2">
                        @forelse ($this->trailers as $video)
                            <div class="space-y-3">
                                <div class="aspect-video overflow-hidden rounded-2xl border border-white/10 bg-black/40">
                                    @if (data_get($video, 'embed_url') || data_get($video, 'url'))
                                        <iframe
                                            src="{{ data_get($video, 'embed_url') ?? data_get($video, 'url') }}"
                                            title="{{ data_get($video, 'name') ?? 'Trailer' }}"
                                            class="h-full w-full"
                                            allowfullscreen
                                        ></iframe>
                                    @else
                                        <div class="flex h-full items-center justify-center text-sm text-zinc-400">
                                            Video preview unavailable
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-medium text-white">{{ data_get($video, 'name') ?? 'Trailer' }}</p>
                                    @if ($published = data_get($video, 'published_at'))
                                        <p class="text-xs text-zinc-500">Published {{ $published }}</p>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-zinc-400">Trailers are not available yet.</p>
                        @endforelse
                    </div>
                </section>
            @elseif ($activeTab === 'reviews')
                <section class="space-y-4">
                    <h2 class="text-xl font-semibold text-white">Reviews</h2>
                    <p class="text-sm text-zinc-400">Community reviews will appear here once they are available.</p>
                </section>
            @elseif ($activeTab === 'translations')
                <section class="space-y-4">
                    <h2 class="text-xl font-semibold text-white">Available Translations</h2>
                    <div class="overflow-hidden rounded-2xl border border-white/10">
                        <table class="min-w-full divide-y divide-white/10 text-sm">
                            <thead class="bg-zinc-900/60 text-xs uppercase tracking-widest text-zinc-400">
                                <tr>
                                    <th class="px-4 py-3 text-left">Locale</th>
                                    <th class="px-4 py-3 text-left">Title</th>
                                    <th class="px-4 py-3 text-left">Tagline</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5">
                                @php
                                    $translationLocales = collect($this->availableTranslations)
                                        ->flatten()
                                        ->unique()
                                        ->values();
                                @endphp
                                @forelse ($translationLocales as $locale)
                                    <tr class="bg-zinc-900/30">
                                        <td class="px-4 py-3 font-medium uppercase text-zinc-300">{{ $locale }}</td>
                                        <td class="px-4 py-3 text-zinc-200">{{ data_get($movie->translations, 'title.'.$locale) ?? $movie->title }}</td>
                                        <td class="px-4 py-3 text-zinc-400">{{ data_get($movie->translations, 'tagline.'.$locale) ?? '—' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-4 py-6 text-center text-sm text-zinc-400">No translated content has been imported yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            @endif
        </div>
    </div>
</div>
