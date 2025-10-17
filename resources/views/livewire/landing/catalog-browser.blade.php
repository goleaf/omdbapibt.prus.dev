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
                <div class="space-y-6 rounded-3xl border border-slate-800/80 bg-slate-950/70 p-6 shadow-inner shadow-emerald-500/5 ring-1 ring-slate-800/60 lg:sticky lg:top-24">
                    <header class="flex items-start justify-between gap-4">
                        <div class="flex items-start gap-4">
                            <span class="flex size-11 items-center justify-center rounded-2xl bg-emerald-400/10 ring-1 ring-inset ring-emerald-400/40">
                                <flux:icon icon="sparkles" class="size-5 text-emerald-200" />
                            </span>
                            <div class="space-y-1.5">
                                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-emerald-300">Collections</p>
                                <h3 class="text-lg font-semibold text-white">Explore curated journeys</h3>
                                <p class="text-sm text-slate-300/90">
                                    Pick a collection to refresh the feed with a distinct programming mood.
                                </p>
                            </div>
                        </div>
                        <flux:button
                            variant="ghost"
                            size="xs"
                            icon-leading="x-mark"
                            data-catalog-sidebar-close
                            aria-controls="catalog-sidebar-{{ $this->getId() }}"
                            class="shrink-0"
                        >
                            Close
                        </flux:button>
                    </header>

                    <ul class="space-y-3">
                        @foreach ($collections as $collectionKey => $collection)
                            @php
                                $isActive = $activeCollection === $collectionKey;

                                $metaTags = collect([
                                    ! empty($collection['minimum_rating'])
                                        ? 'Rating ≥ '.number_format((float) $collection['minimum_rating'], 1)
                                        : null,
                                    ! empty($collection['released_within_days'])
                                        ? 'New • Last '.$collection['released_within_days'].' days'
                                        : null,
                                    ! empty($collection['minimum_popularity'])
                                        ? 'Popularity ≥ '.$collection['minimum_popularity']
                                        : null,
                                    ! empty($collection['released_after_year'])
                                        ? 'Since '.$collection['released_after_year']
                                        : null,
                                ])->filter()->values()->all();

                                $baseButtonClasses = 'group relative block w-full overflow-hidden rounded-2xl border border-slate-800/80 bg-slate-950/60 p-5 text-left transition duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-300/60 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-950';
                                $activeButtonClasses = $isActive
                                    ? 'border-emerald-400/70 bg-gradient-to-br from-emerald-500/15 via-slate-900/70 to-slate-950/80 shadow-[0_12px_35px_-15px_rgba(16,185,129,0.55)] ring-1 ring-inset ring-emerald-400/25'
                                    : 'hover:border-emerald-300/50 hover:bg-slate-900/70';
                            @endphp
                            <li>
                                <button
                                    type="button"
                                    class="{{ $baseButtonClasses }} {{ $activeButtonClasses }}"
                                    wire:click="setCollection('{{ $collectionKey }}')"
                                    wire:key="collection-card-{{ $collectionKey }}"
                                    aria-pressed="{{ $isActive ? 'true' : 'false' }}"
                                >
                                    <span class="pointer-events-none absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-emerald-400/0 via-emerald-400/30 to-emerald-400/0 opacity-0 transition duration-200 group-hover:opacity-100 {{ $isActive ? 'opacity-100' : '' }}"></span>
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="space-y-2">
                                            <p class="text-sm font-semibold text-white">
                                                {{ $collection['label'] }}
                                            </p>
                                            @if (! empty($collection['tagline']))
                                                <p class="text-xs font-medium text-emerald-200/90">
                                                    {{ $collection['tagline'] }}
                                                </p>
                                            @endif
                                            @if (! empty($collection['description']))
                                                <p class="text-xs leading-relaxed text-slate-300">
                                                    {{ $collection['description'] }}
                                                </p>
                                            @endif
                                        </div>
                                        <div class="flex flex-col items-end gap-2">
                                            @if ($isActive)
                                                <span class="inline-flex items-center gap-1 rounded-full border border-emerald-400/40 bg-emerald-500/10 px-2.5 py-1 text-[11px] font-semibold uppercase tracking-[0.2em] text-emerald-200">
                                                    <flux:icon icon="check" class="size-3.5" />
                                                    Active
                                                </span>
                                            @else
                                                <span class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-slate-800/80 bg-slate-900/80 text-slate-500 transition group-hover:border-emerald-300/50 group-hover:text-emerald-200">
                                                    <flux:icon icon="arrow-up-right" class="size-4" />
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    @if ($metaTags !== [])
                                        <div class="mt-4 flex flex-wrap gap-2">
                                            @foreach ($metaTags as $metaTag)
                                                <span class="inline-flex items-center rounded-full border border-emerald-400/30 bg-emerald-500/10 px-2.5 py-1 text-[11px] font-medium text-emerald-100">
                                                    {{ $metaTag }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </aside>
        </div>

        <div class="mt-6 min-w-0 space-y-8 lg:mt-0">
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 3xl:grid-cols-6 xl:gap-6 3xl:gap-4" role="list" aria-live="polite">
                @forelse ($lazyMovies as $index => $movie)
                    <article
                        role="listitem"
                        wire:key="catalog-card-{{ $movie['id'] }}-{{ $index }}"
                        class="group relative overflow-hidden rounded-3xl border border-slate-800/70 bg-slate-900/60 p-4 shadow-sm transition duration-200 hover:border-emerald-400/60 hover:shadow-lg"
                    >
                        <div class="relative aspect-[2/3] overflow-hidden rounded-2xl border border-slate-800/70 bg-slate-950/70">
                            @if (! empty($movie['poster_path']))
                                <img
                                    src="{{ $movie['poster_path'] }}"
                                    alt="{{ $movie['title'] }} poster"
                                    loading="lazy"
                                    class="h-full w-full object-cover transition duration-300 ease-out group-hover:scale-105"
                                >
                            @else
                                <div class="flex h-full w-full items-center justify-center text-xs text-slate-400">
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
                            @if (! empty($movie['tags']))
                                <p class="text-xs uppercase tracking-[0.35em] text-purple-300">
                                    {{ $movie['tags'] }}
                                </p>
                            @endif
                            @if (! empty($movie['genres']))
                                <p class="text-xs uppercase tracking-[0.35em] text-slate-400">
                                    {{ $movie['genres'] }}
                                </p>
                            @endif
                        </div>
                        <a
                            href="{{ route('movies.show', ['locale' => app()->getLocale(), 'movie' => $movie['slug']]) }}"
                            class="absolute inset-0"
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
