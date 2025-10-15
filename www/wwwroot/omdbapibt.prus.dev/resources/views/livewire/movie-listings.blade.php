<div class="space-y-10" wire:key="movie-listings">
    <div class="flex flex-col gap-2 text-sm text-slate-300 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-xs uppercase tracking-[0.3em] text-amber-400">Browse</p>
            <h2 class="text-2xl font-semibold text-white sm:text-3xl">Featured Movies</h2>
        </div>
        @if ($visibleRange)
            <p class="text-sm text-slate-400">
                Showing <span class="font-semibold text-white">{{ $visibleRange[0] }}&ndash;{{ $visibleRange[1] }}</span>
                of <span class="font-semibold text-white">{{ $this->totalMovies() }}</span> titles
            </p>
        @endif
    </div>

    <div class="grid grid-cols-2 gap-5 sm:grid-cols-3 lg:grid-cols-4 2xl:grid-cols-6">
        @foreach ($this->visibleMovies() as $index => $movie)
            <article wire:key="movie-card-{{ $index }}" class="group flex flex-col rounded-2xl border border-white/5 bg-slate-900/40 p-3 shadow-sm backdrop-blur transition hover:border-amber-400/50 hover:shadow-amber-500/10">
                <div class="relative aspect-[2/3] overflow-hidden rounded-xl bg-slate-900 ring-1 ring-white/5">
                    <div class="poster-placeholder absolute inset-0 bg-gradient-to-br from-slate-800 via-slate-900 to-slate-950 animate-pulse"></div>
                    <img
                        loading="lazy"
                        src="{{ $movie['poster'] }}"
                        alt="{{ $movie['title'] }} poster"
                        class="poster-image absolute inset-0 h-full w-full object-cover transition-opacity duration-500 opacity-0"
                        onload="this.classList.remove('opacity-0'); this.previousElementSibling?.classList.add('hidden');"
                        decoding="async"
                    >
                </div>
                <div class="mt-4 flex flex-1 flex-col gap-3">
                    <header class="space-y-1">
                        <h3 class="text-base font-semibold text-white sm:text-lg">{{ $movie['title'] }}</h3>
                        <p class="text-sm text-slate-400">{{ $movie['year'] }} &middot; {{ implode(' / ', $movie['genres']) }}</p>
                    </header>
                    @if ($movie['overview'])
                        <p class="line-clamp-3 text-sm leading-relaxed text-slate-300">{{ $movie['overview'] }}</p>
                    @endif
                    <footer class="mt-auto flex items-center justify-between text-sm text-slate-300">
                        @if ($movie['rating'])
                            <span class="inline-flex items-center gap-1 rounded-full bg-amber-500/10 px-3 py-1 font-medium text-amber-300">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                    <path d="M12 2.5l2.69 6.3 6.81.46-5.18 4.5 1.6 6.74L12 16.9 6.08 20.5l1.6-6.74-5.18-4.5 6.81-.46L12 2.5z" />
                                </svg>
                                {{ number_format($movie['rating'], 1) }}
                            </span>
                        @endif
                        @if ($movie['runtime'])
                            <span>{{ $movie['runtime'] }} min</span>
                        @endif
                    </footer>
                </div>
            </article>
        @endforeach
    </div>

    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <button
                type="button"
                wire:click="goToPage({{ max(1, $this->page - 1) }})"
                @disabled($this->page <= 1)
                class="inline-flex items-center gap-2 rounded-full border border-white/10 px-4 py-2 text-sm font-medium text-white transition hover:border-amber-400/60 hover:text-amber-200 disabled:cursor-not-allowed disabled:border-white/5 disabled:text-slate-500"
            >
                &larr; Previous
            </button>
            <div class="hidden items-center gap-2 sm:flex">
                @foreach ($this->pageNumbers() as $pageNumber)
                    <button
                        type="button"
                        wire:click="goToPage({{ $pageNumber }})"
                        class="h-9 min-w-9 rounded-full border px-3 text-sm font-medium transition @class([
                            'border-amber-500 bg-amber-500/10 text-amber-200' => $pageNumber === $this->page,
                            'border-white/10 text-slate-300 hover:border-amber-400/60 hover:text-amber-200' => $pageNumber !== $this->page,
                        ])"
                    >
                        {{ $pageNumber }}
                    </button>
                @endforeach
            </div>
            <button
                type="button"
                wire:click="goToPage({{ min($this->maxPage(), $this->page + 1) }})"
                @disabled($this->page >= $this->maxPage())
                class="inline-flex items-center gap-2 rounded-full border border-white/10 px-4 py-2 text-sm font-medium text-white transition hover:border-amber-400/60 hover:text-amber-200 disabled:cursor-not-allowed disabled:border-white/5 disabled:text-slate-500"
            >
                Next &rarr;
            </button>
        </div>

        @if ($this->page < $this->maxPage())
            <div
                data-infinite-scroll-sentinel
                class="h-1"
                aria-hidden="true"
                wire:key="movie-sentinel-{{ $this->page }}"
            ></div>
        @endif
    </div>
</div>
