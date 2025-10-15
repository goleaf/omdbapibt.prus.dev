<div class="rounded-lg bg-white p-6 shadow">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Personalized suggestions</h2>
            <p class="text-sm text-gray-500">
                Discover films aligned with your watch history and favourite genres.
            </p>
        </div>

        <div class="text-xs text-gray-400" wire:loading.class="animate-pulse">
            Updated {{ now()->diffForHumans() }}
        </div>
    </div>

    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($this->suggestions as $movie)
            <article wire:key="recommendation-{{ $movie->getKey() }}" class="flex flex-col rounded-lg border border-gray-200 bg-gray-50 p-4">
                <header class="flex items-start justify-between gap-3">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">{{ $movie->title }}</h3>
                        <p class="text-xs text-gray-500">
                            {{ $movie->year }} &bull; {{ ucfirst($movie->media_type ?? 'movie') }}
                        </p>
                    </div>

                    <span class="inline-flex items-center rounded-full bg-emerald-100 px-2 py-1 text-xs font-semibold text-emerald-700">
                        Score {{ number_format($movie->recommendation_score ?? 0, 2) }}
                    </span>
                </header>

                <p class="mt-3 line-clamp-3 text-sm text-gray-600">
                    {{ $movie->overview['en'] ?? $movie->plot ?? 'No synopsis available yet.' }}
                </p>

                <footer class="mt-auto pt-4 text-xs text-gray-500">
                    <span class="font-medium text-gray-700">Genres:</span>
                    {{ $movie->genres->pluck('name')->take(3)->implode(', ') ?: 'TBD' }}
                </footer>
            </article>
        @empty
            <div class="col-span-full rounded-lg border border-dashed border-gray-300 bg-white p-8 text-center text-sm text-gray-500">
                Start watching movies to unlock personalised recommendations.
            </div>
        @endforelse
    </div>
</div>
