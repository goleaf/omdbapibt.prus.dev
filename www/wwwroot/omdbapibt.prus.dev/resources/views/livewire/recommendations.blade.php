<div class="rounded-lg bg-white p-6 shadow">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-xl font-semibold text-gray-900">Recommended for you</h2>
            <p class="text-sm text-gray-600">
                Personalised picks generated from your watch history and what's trending right now.
            </p>
        </div>
        <div class="flex items-center gap-3">
            @if($lastUpdated)
                <span class="text-sm text-gray-500">Updated {{ $lastUpdated }}</span>
            @endif
            <button
                wire:click="refresh"
                type="button"
                class="inline-flex items-center rounded-md border border-blue-500 px-4 py-2 text-sm font-semibold text-blue-600 transition hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1"
            >
                Refresh
            </button>
        </div>
    </div>

    @if(! $hasWatchHistory)
        <div class="mt-6 rounded-md border border-dashed border-blue-200 bg-blue-50 p-4 text-sm text-blue-800">
            We do not have enough viewing history yet, so these suggestions lean on what other members are enjoying most.
        </div>
    @endif

    @if(empty($recommendations))
        <div class="mt-6 rounded-md border border-gray-200 bg-gray-50 p-6 text-center text-gray-600">
            No recommendations are available just yet. Check back after adding a few titles to your watch history.
        </div>
    @else
        <ul class="mt-6 grid gap-5 md:grid-cols-2">
            @foreach($recommendations as $recommendation)
                <li class="flex gap-4 rounded-lg border border-gray-200 p-4 shadow-sm transition hover:shadow">
                    @if(! empty($recommendation['poster_path']))
                        <img
                            src="{{ $recommendation['poster_path'] }}"
                            alt="{{ $recommendation['title'] }} poster"
                            class="h-28 w-20 rounded object-cover"
                        >
                    @else
                        <div class="flex h-28 w-20 items-center justify-center rounded bg-gray-200 text-lg font-semibold text-gray-500">
                            {{ strtoupper(mb_substr($recommendation['title'], 0, 1)) }}
                        </div>
                    @endif
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900">
                            {{ $recommendation['title'] }}
                        </h3>
                        <p class="text-sm text-gray-500">
                            @if(! empty($recommendation['year']))
                                {{ $recommendation['year'] }} •
                            @endif
                            {{ ucfirst($recommendation['media_type'] ?? 'movie') }}
                        </p>
                        <p class="mt-3 text-sm text-gray-600">
                            {{ $recommendation['reason'] }}
                        </p>
                        <p class="mt-3 text-xs font-medium uppercase tracking-wide text-blue-600">
                            Match score: {{ number_format((float) $recommendation['score'], 1) }}
                        </p>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif
</div>
