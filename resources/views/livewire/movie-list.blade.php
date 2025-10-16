<div class="space-y-8">
    <div class="rounded-3xl border border-zinc-200/80 bg-white/70 p-6 shadow-sm backdrop-blur-sm dark:border-zinc-700/60 dark:bg-zinc-900/60">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-white">Browse Movies</h2>
                <p class="text-sm text-zinc-600 dark:text-zinc-300">Refine the catalogue by applying one or more filters.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <flux:button variant="ghost" size="sm" wire:click="clearAll" wire:loading.attr="disabled">
                    Clear filters
                </flux:button>
            </div>
        </div>

        @php
            $activeFilters = collect([
                'genreId' => optional($genres->firstWhere('id', $genreId))?->localizedName(),
                'year' => $year,
                'rating' => $rating ? number_format($rating, 1) . '+' : null,
                'languageId' => optional($languages->firstWhere('id', $languageId))?->localizedName(),
                'countryId' => optional($countries->firstWhere('id', $countryId))?->localizedName(),
            ])->filter();
        @endphp

        @if ($activeFilters->isNotEmpty())
            <div class="mt-4 flex flex-wrap gap-2">
                @foreach ($activeFilters as $property => $label)
                    <flux:button variant="subtle" size="xs" icon-trailing="x-mark" wire:click="clear('{{ $property }}')" wire:key="active-filter-{{ $property }}">
                        {{ $label }}
                    </flux:button>
                @endforeach
            </div>
        @endif

        <div class="mt-6 space-y-5">
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-zinc-700 dark:text-zinc-200">Genre</p>
                    @if ($genreId)
                        <flux:button variant="ghost" size="xs" icon-leading="x-mark" wire:click="clear('genreId')">
                            Clear
                        </flux:button>
                    @endif
                </div>
                <div class="flex flex-wrap gap-2">
                    @foreach ($genres as $genre)
                        <flux:button
                            size="sm"
                            variant="{{ (int) $genreId === $genre->id ? 'primary' : 'outline' }}"
                            wire:click="$set('genreId', {{ $genre->id }})"
                            wire:key="genre-{{ $genre->id }}"
                        >
                            {{ $genre->localizedName() }}
                        </flux:button>
                    @endforeach
                </div>
            </div>

            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-zinc-700 dark:text-zinc-200">Release year</p>
                    @if ($year)
                        <flux:button variant="ghost" size="xs" icon-leading="x-mark" wire:click="clear('year')">
                            Clear
                        </flux:button>
                    @endif
                </div>
                <div class="flex flex-wrap gap-2">
                    @foreach ($availableYears as $filterYear)
                        <flux:button
                            size="sm"
                            variant="{{ (int) $year === (int) $filterYear ? 'primary' : 'outline' }}"
                            wire:click="$set('year', {{ (int) $filterYear }})"
                            wire:key="year-{{ $filterYear }}"
                        >
                            {{ $filterYear }}
                        </flux:button>
                    @endforeach
                </div>
            </div>

            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-zinc-700 dark:text-zinc-200">Minimum rating</p>
                    @if (! is_null($rating))
                        <flux:button variant="ghost" size="xs" icon-leading="x-mark" wire:click="clear('rating')">
                            Clear
                        </flux:button>
                    @endif
                </div>
                <div class="flex flex-wrap gap-2">
                    @foreach ($ratingOptions as $option)
                        <flux:button
                            size="sm"
                            variant="{{ (float) $rating === (float) $option ? 'primary' : 'outline' }}"
                            wire:click="$set('rating', {{ $option }})"
                            wire:key="rating-{{ $option }}"
                        >
                            {{ number_format($option, 1) }}+
                        </flux:button>
                    @endforeach
                </div>
            </div>

            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-zinc-700 dark:text-zinc-200">Language</p>
                    @if ($languageId)
                        <flux:button variant="ghost" size="xs" icon-leading="x-mark" wire:click="clear('languageId')">
                            Clear
                        </flux:button>
                    @endif
                </div>
                <div class="flex flex-wrap gap-2">
                    @foreach ($languages as $language)
                        <flux:button
                            size="sm"
                            variant="{{ (int) $languageId === $language->id ? 'primary' : 'outline' }}"
                            wire:click="$set('languageId', {{ $language->id }})"
                            wire:key="language-{{ $language->id }}"
                        >
                            {{ $language->localizedName() }}
                        </flux:button>
                    @endforeach
                </div>
            </div>

            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-zinc-700 dark:text-zinc-200">Country</p>
                    @if ($countryId)
                        <flux:button variant="ghost" size="xs" icon-leading="x-mark" wire:click="clear('countryId')">
                            Clear
                        </flux:button>
                    @endif
                </div>
                <div class="flex flex-wrap gap-2">
                    @foreach ($countries as $country)
                        <flux:button
                            size="sm"
                            variant="{{ (int) $countryId === $country->id ? 'primary' : 'outline' }}"
                            wire:click="$set('countryId', {{ $country->id }})"
                            wire:key="country-{{ $country->id }}"
                        >
                            {{ $country->localizedName() }}
                        </flux:button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="relative space-y-6">
        <div wire:loading.flex class="absolute inset-0 z-10 items-center justify-center rounded-3xl bg-white/70 backdrop-blur-sm dark:bg-zinc-900/70">
            <div class="flex items-center gap-2 text-sm font-medium text-zinc-700 dark:text-zinc-200">
                <flux:icon icon="loading" class="size-5 animate-spin" />
                Loading movies...
            </div>
        </div>

        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($movies as $movie)
                <article class="group flex flex-col overflow-hidden rounded-3xl border border-zinc-200/70 bg-white/80 shadow-sm transition hover:-translate-y-1 hover:shadow-md dark:border-zinc-700/60 dark:bg-zinc-900/60">
                    <div class="relative aspect-[2/3] w-full overflow-hidden bg-zinc-200/80 dark:bg-zinc-800/80">
                        @if ($movie->poster_path)
                            <img src="{{ $movie->poster_path }}" alt="{{ $movie->localizedTitle() }} poster" class="h-full w-full object-cover transition duration-300 group-hover:scale-105" loading="lazy">
                        @else
                            <div class="flex h-full items-center justify-center text-sm font-medium text-zinc-500 dark:text-zinc-400">
                                No poster available
                            </div>
                        @endif
                        @if (! is_null($movie->vote_average))
                            <div class="absolute right-3 top-3 flex items-center gap-1 rounded-full bg-zinc-900/80 px-3 py-1 text-xs font-semibold text-white shadow">
                                <span aria-hidden="true">⭐</span>
                                {{ number_format((float) $movie->vote_average, 1) }}
                            </div>
                        @endif
                    </div>
                    <div class="flex flex-1 flex-col gap-3 p-5">
                        <header>
                            <h3 class="text-base font-semibold text-zinc-900 dark:text-white">{{ $movie->localizedTitle() }}</h3>
                            <p class="text-sm text-zinc-600 dark:text-zinc-300">
                                @if ($movie->year)
                                    <span>{{ $movie->year }}</span>
                                @elseif ($movie->release_date)
                                    <span>{{ $movie->release_date->format('Y') }}</span>
                                @endif
                                @if ($movie->runtime)
                                    <span class="before:mx-2 before:content-['•']">{{ $movie->runtime }} min</span>
                                @endif
                            </p>
                        </header>
                        @if ($movie->genres->isNotEmpty())
                            <div class="flex flex-wrap gap-2">
                                @foreach ($movie->genres->take(3) as $movieGenre)
                                    <span class="rounded-full bg-zinc-100 px-3 py-1 text-xs font-medium text-zinc-700 dark:bg-zinc-800/80 dark:text-zinc-200">{{ $movieGenre->name }}</span>
                                @endforeach
                            </div>
                        @endif
                        @if ($movie->plot)
                            <p class="line-clamp-3 text-sm text-zinc-600 dark:text-zinc-300">{{ $movie->plot }}</p>
                        @endif
                        <div class="mt-auto flex flex-wrap gap-2 text-xs text-zinc-500 dark:text-zinc-400">
                            @if ($movie->countries->isNotEmpty())
                                <span class="inline-flex items-center gap-1">
                                    <span aria-hidden="true">🌍</span>
                                    {{ $movie->countries->pluck('name')->take(2)->join(', ') }}
                                </span>
                            @endif
                            @if ($movie->languages->isNotEmpty())
                                <span class="inline-flex items-center gap-1">
                                    <span aria-hidden="true">💬</span>
                                    {{ $movie->languages->pluck('name')->take(2)->join(', ') }}
                                </span>
                            @endif
                        </div>
                    </div>
                </article>
            @empty
                <div class="col-span-full rounded-3xl border border-dashed border-zinc-300/80 bg-white/60 p-8 text-center text-sm text-zinc-600 dark:border-zinc-700/60 dark:bg-zinc-900/50 dark:text-zinc-300">
                    No movies match the selected filters. Try adjusting your criteria.
                </div>
            @endforelse
        </div>

        @if ($movies->hasPages())
            @php
                $startPage = max(1, $movies->currentPage() - 2);
                $endPage = min($movies->lastPage(), $movies->currentPage() + 2);
            @endphp
            <div class="flex flex-col gap-4 rounded-3xl border border-zinc-200/70 bg-white/80 p-4 dark:border-zinc-700/60 dark:bg-zinc-900/60 md:flex-row md:items-center md:justify-between">
                <p class="text-sm text-zinc-600 dark:text-zinc-300">
                    Showing
                    <span class="font-semibold text-zinc-900 dark:text-white">{{ $movies->firstItem() }}</span>
                    –
                    <span class="font-semibold text-zinc-900 dark:text-white">{{ $movies->lastItem() }}</span>
                    of
                    <span class="font-semibold text-zinc-900 dark:text-white">{{ $movies->total() }}</span>
                    results
                </p>
                <div class="flex flex-wrap items-center gap-2">
                    <flux:button variant="outline" size="sm" wire:click="previousPage('page')" wire:loading.attr="disabled" @disabled($movies->onFirstPage())>
                        Previous
                    </flux:button>
                    @foreach (range($startPage, $endPage) as $page)
                        <flux:button
                            variant="{{ $movies->currentPage() === $page ? 'primary' : 'outline' }}"
                            size="sm"
                            wire:click="gotoPage({{ $page }}, 'page')"
                            wire:key="page-link-{{ $page }}"
                        >
                            {{ $page }}
                        </flux:button>
                    @endforeach
                    <flux:button variant="outline" size="sm" wire:click="nextPage('page')" wire:loading.attr="disabled" @disabled(! $movies->hasMorePages())>
                        Next
                    </flux:button>
                </div>
            </div>
        @endif
    </div>
</div>
