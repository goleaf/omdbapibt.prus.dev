<div class="space-y-6" wire:loading.class.delay="opacity-60">
    @if ($watchableType && $watchableId)
        <div>
            @if ($isAuthenticated)
                <button
                    type="button"
                    wire:click="toggle"
                    class="inline-flex items-center gap-2 rounded-md border border-blue-500 px-4 py-2 text-sm font-semibold transition hover:bg-blue-500 hover:text-white"
                >
                    <span class="h-2 w-2 rounded-full {{ $inWatchlist ? 'bg-blue-500' : 'bg-gray-300' }}"></span>
                    <span>{{ $inWatchlist ? 'Remove from Watchlist' : 'Add to Watchlist' }}</span>
                </button>
            @else
                <span class="inline-flex items-center rounded-md bg-blue-50 px-4 py-2 text-sm text-blue-600">
                    Sign in to save this title to your watchlist.
                </span>
            @endif
        </div>
    @endif

    @if ($showList)
        @php($items = $this->items)
        <div class="rounded-lg bg-white p-6 shadow">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold">My Watchlist</h3>
                <span class="text-sm text-gray-500">
                    {{ $items->count() }} {{ \Illuminate\Support\Str::plural('item', $items->count()) }}
                </span>
            </div>

            <div class="mt-4 space-y-4">
                @forelse ($items as $item)
                    <div class="flex items-center justify-between rounded-md border border-gray-200 px-4 py-3">
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $item['title'] ?: 'Untitled' }}</p>
                            <p class="text-xs uppercase tracking-wide text-gray-500">{{ $item['type_label'] }}</p>
                            @if ($item['added_at'])
                                <p class="mt-1 text-xs text-gray-400">Added {{ $item['added_at']->diffForHumans() }}</p>
                            @endif
                        </div>
                        <div class="flex items-center gap-3">
                            @if ($item['poster_path'])
                                <img
                                    src="{{ $item['poster_path'] }}"
                                    alt="{{ $item['title'] }} poster"
                                    class="h-16 w-12 rounded object-cover"
                                />
                            @endif
                            <button
                                type="button"
                                wire:click="removeItem('{{ $item['type'] }}', {{ $item['id'] }})"
                                class="text-sm font-medium text-red-600 hover:text-red-500"
                            >
                                Remove
                            </button>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">
                        You haven't saved anything yet. Explore movies and TV shows to add them to your watchlist.
                    </p>
                @endforelse
            </div>
        </div>
    @endif
</div>
