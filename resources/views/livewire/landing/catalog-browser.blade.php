@php
    $locale = app()->getLocale();
@endphp

<section
    class="space-y-6"
    data-infinite-scroll="true"
    data-component-id="{{ $this->getId() }}"
>
    <div class="rounded-3xl border border-slate-800/60 bg-slate-950/70 p-6 shadow-xl ring-1 ring-emerald-500/10 sm:p-8">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
            <div class="max-w-2xl space-y-3">
                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-emerald-300">Curated Catalog</p>
                <h2 class="text-2xl font-semibold text-white sm:text-3xl">
                    {{ $activeMeta['label'] ?? 'Curated catalog' }}
                </h2>
                @if (! empty($activeMeta['description']))
                    <p class="text-sm text-slate-300 sm:text-base">
                        {{ $activeMeta['description'] }}
                    </p>
                @endif
                @if (! empty($activeMeta['tagline']))
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-emerald-300/80">
                        {{ $activeMeta['tagline'] }}
                    </p>
                @endif
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <flux:button
                    variant="outline"
                    size="sm"
                    icon-leading="adjustments-horizontal"
                    data-catalog-sidebar-toggle
                    aria-controls="catalog-sidebar-{{ $this->getId() }}"
                    aria-expanded="false"
                >
                    Browse collections
                </flux:button>
                <flux:button
                    variant="subtle"
                    size="sm"
                    icon-leading="arrow-path"
                    wire:click="$refresh"
                >
                    Refresh results
                </flux:button>
            </div>
        </div>
        <dl class="mt-6 grid gap-4 text-xs text-slate-300 sm:grid-cols-3">
            <div class="rounded-2xl border border-slate-800/80 bg-slate-900/60 p-4">
                <dt class="uppercase tracking-[0.3em] text-slate-400">Indexed titles</dt>
                <dd class="mt-2 text-2xl font-semibold text-white">{{ number_format($totalResults) }}</dd>
            </div>
            <div class="rounded-2xl border border-slate-800/80 bg-slate-900/60 p-4">
                <dt class="uppercase tracking-[0.3em] text-slate-400">Lazy-loaded pages</dt>
                <dd class="mt-2 text-2xl font-semibold text-white">{{ count($lazyMovies) > 0 ? ceil(count($lazyMovies) / $perPage) : 0 }}</dd>
            </div>
            <div class="rounded-2xl border border-slate-800/80 bg-slate-900/60 p-4">
                <dt class="uppercase tracking-[0.3em] text-slate-400">Infinite scroll</dt>
                <dd class="mt-2 text-2xl font-semibold text-emerald-300">{{ $hasMorePages ? 'Ready' : 'Complete' }}</dd>
            </div>
        </dl>
    </div>

    <div class="lg:grid lg:grid-cols-[280px,minmax(0,1fr)] lg:gap-6">
        <div class="lg:relative">
            <div class="lg:hidden">
                <div data-catalog-sidebar-overlay class="fixed inset-0 z-30 hidden bg-slate-950/70 backdrop-blur-sm"></div>
            </div>

            <aside
                id="catalog-sidebar-{{ $this->getId() }}"
                data-catalog-sidebar
                data-open="false"
                tabindex="-1"
                class="fixed inset-y-0 left-0 z-40 hidden w-full max-w-xs transform bg-slate-950/95 p-6 text-slate-100 shadow-2xl transition-transform duration-200 ease-out focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400 lg:static lg:z-auto lg:block lg:max-w-none lg:translate-x-0 lg:bg-transparent lg:p-0 lg:shadow-none"
                aria-label="Curated collections"
            >
                <div class="space-y-5 rounded-3xl border border-slate-800/80 bg-slate-900/60 p-5 lg:sticky lg:top-24">
                    <div class="flex items-center justify-between gap-3">
                        <p class="text-xs font-semibold uppercase tracking-[0.35em] text-emerald-300">Collections</p>
                        <flux:button
                            variant="ghost"
                            size="xs"
                            icon-leading="x-mark"
                            data-catalog-sidebar-close
                            aria-controls="catalog-sidebar-{{ $this->getId() }}"
                        >
                            Close
                        </flux:button>
                    </div>
                    <ul class="space-y-2">
                        @foreach ($collections as $collectionKey => $collection)
                            @php
                                $isActive = $activeCollection === $collectionKey;
                            @endphp
                            <li>
                                <flux:button
                                    type="button"
                                    class="w-full justify-between text-left"
                                    size="sm"
                                    variant="{{ $isActive ? 'primary' : 'ghost' }}"
                                    color="emerald"
                                    wire:click="setCollection('{{ $collectionKey }}')"
                                    wire:key="collection-pill-{{ $collectionKey }}"
                                    aria-pressed="{{ $isActive ? 'true' : 'false' }}"
                                >
                                    <span class="flex flex-1 flex-col text-left">
                                        <span class="text-sm font-semibold text-inherit">
                                            {{ $collection['label'] }}
                                        </span>
                                        @if (! empty($collection['tagline']))
                                            <span class="text-xs text-slate-300">{{ $collection['tagline'] }}</span>
                                        @endif
                                    </span>
                                    @if ($isActive)
                                        <flux:icon icon="check" class="size-4 text-emerald-100" />
                                    @endif
                                </flux:button>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </aside>
        </div>

        <div class="mt-6 min-w-0 space-y-8 lg:mt-0">
            <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3" role="list" aria-live="polite">
                @forelse ($lazyMovies as $index => $movie)
                    <article
                        role="listitem"
                        wire:key="catalog-card-{{ $movie['id'] }}-{{ $index }}"
                        class="group relative overflow-hidden rounded-3xl border border-slate-800/70 bg-slate-900/60 p-4 shadow-sm transition duration-200 hover:border-emerald-400/60 hover:shadow-lg"
                    >
                        <div class="relative overflow-hidden rounded-2xl border border-slate-800/70 bg-slate-950/70">
                            @if (! empty($movie['poster_path']))
                                <img
                                    src="{{ $movie['poster_path'] }}"
                                    alt="{{ $movie['title'] }} poster"
                                    loading="lazy"
                                    class="h-full w-full object-cover transition duration-300 ease-out group-hover:scale-105"
                                >
                            @else
                                <div class="flex h-64 items-center justify-center text-xs text-slate-400">
                                    Poster unavailable
                                </div>
                            @endif
                            <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-950/10 to-transparent opacity-0 transition duration-300 group-hover:opacity-100"></div>
                        </div>
                        <div class="mt-4 space-y-2">
                            <div class="flex flex-wrap items-center gap-2">
                                @if (! empty($movie['release_year']))
                                    <flux:badge variant="solid" color="emerald" size="sm">
                                        {{ $movie['release_year'] }}
                                    </flux:badge>
                                @endif
                                @if (! empty($movie['vote_average']))
                                    <flux:badge variant="solid" color="amber" size="sm" icon="star">
                                        {{ $movie['vote_average'] }}
                                    </flux:badge>
                                @endif
                            </div>
                            <h3 class="text-lg font-semibold text-white">
                                {{ $movie['title'] }}
                            </h3>
                            @if (! empty($movie['tagline']))
                                <p class="text-sm text-slate-300">
                                    {{ $movie['tagline'] }}
                                </p>
                            @endif
                            @if (! empty($movie['genres']))
                                <p class="text-xs uppercase tracking-[0.35em] text-slate-400">
                                    {{ $movie['genres'] }}
                                </p>
                            @endif
                        </div>
                        <a
                            href="{{ route('movies.show', ['locale' => $locale, 'movie' => $movie['slug']]) }}"
                            class="focus-card absolute inset-0"
                            aria-label="Open {{ $movie['title'] }} details"
                        ></a>
                    </article>
                @empty
                    <div class="col-span-full rounded-3xl border border-slate-800/60 bg-slate-900/60 p-10 text-center text-sm text-slate-300">
                        There are no titles in this collection yet. Try switching collections or updating your ingest configuration.
                    </div>
                @endforelse
            </div>

            <div
                class="flex flex-col items-center gap-3"
                data-infinite-scroll-target
                data-can-load-more="{{ $hasMorePages ? 'true' : 'false' }}"
            >
                @if ($hasMorePages)
                    <flux:button
                        variant="outline"
                        size="sm"
                        icon-leading="arrow-down"
                        wire:click="loadMore"
                    >
                        Load more titles
                    </flux:button>
                    <p class="text-xs text-slate-400">Cards load automatically as you reach this point.</p>
                @else
                    <p class="text-xs text-slate-400">You have reached the end of this curated feed.</p>
                @endif
            </div>
        </div>
    </div>
</section>
