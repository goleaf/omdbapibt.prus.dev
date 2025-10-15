<div class="space-y-6">
    <div class="rounded-xl border border-slate-800/60 bg-slate-900/60 p-6 shadow-sm shadow-slate-950/30">
        <form class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            <label class="flex flex-col gap-2">
                <span class="text-xs font-semibold uppercase tracking-wide text-slate-400">Search</span>
                <input
                    type="search"
                    wire:model.live="search"
                    placeholder="Search by title or name"
                    class="w-full rounded-lg border border-slate-800 bg-slate-950/60 px-3 py-2 text-sm text-slate-100 placeholder:text-slate-500 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/30"
                />
            </label>

            <label class="flex flex-col gap-2">
                <span class="text-xs font-semibold uppercase tracking-wide text-slate-400">Media type</span>
                <select
                    wire:model.live="mediaType"
                    class="w-full rounded-lg border border-slate-800 bg-slate-950/60 px-3 py-2 text-sm text-slate-100 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/30"
                >
                    <option value="">All media</option>
                    <option value="movie">Movies</option>
                    <option value="tv">TV shows</option>
                </select>
            </label>

            <label class="flex flex-col gap-2">
                <span class="text-xs font-semibold uppercase tracking-wide text-slate-400">Date range</span>
                <select
                    wire:model.live="dateRange"
                    class="w-full rounded-lg border border-slate-800 bg-slate-950/60 px-3 py-2 text-sm text-slate-100 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/30"
                >
                    <option value="30">Last 30 days</option>
                    <option value="7">Last 7 days</option>
                    <option value="90">Last 90 days</option>
                    <option value="365">Last 12 months</option>
                    <option value="">All time</option>
                </select>
            </label>

            <div class="flex items-end">
                <button
                    type="button"
                    wire:click="resetFilters"
                    class="w-full rounded-lg border border-slate-700/70 bg-slate-900/60 px-3 py-2 text-sm font-semibold text-slate-200 transition hover:border-emerald-400 hover:text-emerald-200"
                >
                    Reset filters
                </button>
            </div>
        </form>
    </div>

    <div class="rounded-xl border border-slate-800/60 bg-slate-900/60 shadow-sm shadow-slate-950/30">
        @if ($histories->isEmpty())
            <div class="px-6 py-16 text-center">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-slate-800/70 text-emerald-400">
                    <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M4 4h16v12H4z" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M4 16l4 4h8l4-4" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M10 8l2 2 2-2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>
                <h2 class="mt-6 text-lg font-semibold text-slate-100">No watch activity yet</h2>
                <p class="mt-2 text-sm text-slate-400">Start streaming movies and shows to build your watch history. New sessions will appear here instantly.</p>
            </div>
        @else
            <div class="divide-y divide-slate-800/70">
                @foreach ($histories as $history)
                    @php
                        $watchable = $history->watchable;
                        $title = $watchable?->title ?? $watchable?->name ?? 'Unknown title';
                        $secondary = $watchable?->original_title ?? $watchable?->original_name ?? null;
                        $watchedAt = $history->watched_at ?? $history->created_at;
                    @endphp

                    <div class="flex flex-col gap-4 px-6 py-5 md:flex-row md:items-center md:justify-between" wire:key="history-{{ $history->id }}">
                        <div>
                            <p class="text-base font-semibold text-slate-100">{{ $title }}</p>
                            @if ($secondary && $secondary !== $title)
                                <p class="text-sm text-slate-400">{{ $secondary }}</p>
                            @endif
                            <p class="mt-2 flex flex-wrap items-center gap-3 text-xs font-medium uppercase tracking-wide text-slate-400">
                                <span class="rounded-full bg-slate-800/70 px-2 py-0.5 text-emerald-300">{{ $history->mediaTypeLabel() }}</span>
                                <span>Watched {{ optional($watchedAt)->diffForHumans() }}</span>
                            </p>
                        </div>

                        <div class="flex flex-col items-start gap-2 text-sm text-slate-300 md:items-end">
                            @if (! is_null($history->progress_percent))
                                <div class="flex items-center gap-3">
                                    <span class="text-xs font-semibold uppercase tracking-wide text-slate-500">Progress</span>
                                    <span class="rounded-md bg-emerald-500/10 px-2 py-1 font-semibold text-emerald-300">{{ $history->progress_percent }}%</span>
                                </div>
                            @endif

                            <div class="flex items-center gap-2 text-xs uppercase tracking-wide">
                                <span class="h-2 w-2 rounded-full {{ $history->completed ? 'bg-emerald-400' : 'bg-amber-400' }}"></span>
                                <span class="text-slate-400">{{ $history->completed ? 'Completed' : 'In progress' }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="border-t border-slate-800/70 px-6 py-4">
                {{ $histories->links() }}
            </div>
        @endif
    </div>
</div>
