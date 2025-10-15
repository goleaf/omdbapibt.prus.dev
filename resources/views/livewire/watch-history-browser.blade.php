@php
    use App\Models\Movie;
    use App\Models\TvShow;

    $locale = app()->getLocale();
@endphp

<div class="space-y-8">
    <section class="rounded-3xl border border-slate-800/60 bg-slate-900/60 p-6 shadow-xl shadow-emerald-500/5">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-slate-100">Browse history</h2>
                <p class="text-sm text-slate-400">Filter and search every title you've watched on OMDb Stream.</p>
            </div>
            <div class="flex w-full flex-col gap-4 sm:flex-row sm:items-center sm:justify-end">
                <div class="relative w-full sm:w-64">
                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-500">
                        <flux:icon icon="magnifying-glass" class="size-4" />
                    </span>
                    <input
                        type="search"
                        wire:model.live.debounce.400ms="search"
                        placeholder="Search titles, originals, or slugs"
                        class="w-full rounded-2xl border border-slate-800/60 bg-slate-950/60 py-2.5 pl-10 pr-4 text-sm text-slate-100 placeholder:text-slate-500 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/30"
                    >
                </div>
                <div class="flex items-center gap-2">
                    @php
                        $typeOptions = [
                            'all' => 'All entries',
                            'movie' => 'Movies',
                            'tv' => 'Series',
                        ];
                    @endphp
                    @foreach ($typeOptions as $optionValue => $label)
                        <button
                            type="button"
                            wire:click="$set('type', '{{ $optionValue }}')"
                            @class([
                                'rounded-full border px-4 py-2 text-xs font-semibold transition',
                                'border-emerald-500 bg-emerald-500/20 text-emerald-100' => $type === $optionValue,
                                'border-slate-800 bg-slate-950/40 text-slate-300 hover:border-emerald-400 hover:text-emerald-200' => $type !== $optionValue,
                            ])
                        >
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <dl class="mt-8 grid gap-4 sm:grid-cols-3">
            <div class="rounded-2xl border border-slate-800/70 bg-slate-950/40 p-4">
                <dt class="text-xs uppercase tracking-wide text-slate-400">Total plays</dt>
                <dd class="mt-2 text-2xl font-semibold text-slate-100">{{ number_format($metrics['total']) }}</dd>
            </div>
            <div class="rounded-2xl border border-slate-800/70 bg-slate-950/40 p-4">
                <dt class="text-xs uppercase tracking-wide text-slate-400">Unique titles</dt>
                <dd class="mt-2 text-2xl font-semibold text-slate-100">{{ number_format($metrics['unique']) }}</dd>
            </div>
            <div class="rounded-2xl border border-slate-800/70 bg-slate-950/40 p-4">
                <dt class="text-xs uppercase tracking-wide text-slate-400">Most recent</dt>
                <dd class="mt-2 text-base text-slate-200">
                    @if ($metrics['last_watched_at'])
                        {{ $metrics['last_watched_at']->timezone(config('app.timezone'))->toDayDateTimeString() }}
                    @else
                        <span class="text-slate-500">No activity yet</span>
                    @endif
                </dd>
            </div>
        </dl>
    </section>

    <section class="relative rounded-3xl border border-slate-800/60 bg-slate-900/60 p-6">
        <div
            wire:loading.flex
            class="absolute inset-0 z-10 items-center justify-center rounded-3xl bg-slate-950/70 backdrop-blur"
        >
            <div class="flex items-center gap-3 text-sm font-semibold text-slate-300">
                <flux:icon icon="loading" class="size-5 animate-spin" />
                Loading watch history…
            </div>
        </div>

        <div class="space-y-4">
            @forelse ($histories as $history)
                @php
                    $watchable = $history->watchable;
                    $title = 'Unavailable title';
                    $subtitleParts = [];
                    $poster = null;
                    $typeLabel = 'Entry';
                    $route = null;

                    if ($watchable instanceof Movie) {
                        $rawTitle = $watchable->title;
                        if (is_array($rawTitle)) {
                            $title = $rawTitle[$locale] ?? $rawTitle['en'] ?? reset($rawTitle) ?? 'Untitled movie';
                        } else {
                            $title = $rawTitle ?: 'Untitled movie';
                        }
                        if ($watchable->release_date) {
                            $subtitleParts[] = $watchable->release_date->format('M j, Y');
                        } elseif ($watchable->year) {
                            $subtitleParts[] = $watchable->year;
                        }
                        if ($watchable->runtime) {
                            $subtitleParts[] = $watchable->runtime . ' min';
                        }
                        $poster = $watchable->poster_path;
                        $typeLabel = 'Movie';
                        $route = route('movies.show', ['locale' => $locale, 'slug' => $watchable->slug]);
                    } elseif ($watchable instanceof TvShow) {
                        $title = $watchable->name ?: 'Untitled series';
                        if ($watchable->first_air_date) {
                            $subtitleParts[] = $watchable->first_air_date->format('Y');
                        }
                        if ($watchable->episode_run_time) {
                            $subtitleParts[] = $watchable->episode_run_time . ' min avg';
                        }
                        $poster = $watchable->poster_path;
                        $typeLabel = 'Series';
                        $route = route('shows.show', ['locale' => $locale, 'slug' => $watchable->slug]);
                    }

                    $subtitle = implode(' • ', array_filter($subtitleParts));
                    $watchedAt = $history->watched_at?->timezone(config('app.timezone'));
                @endphp
                <article
                    wire:key="history-{{ $history->id }}"
                    class="flex flex-col gap-4 rounded-2xl border border-slate-800/60 bg-slate-950/40 p-4 transition hover:border-emerald-400/70 hover:bg-emerald-500/10 lg:flex-row lg:items-center"
                >
                    <div class="flex items-center gap-4 lg:w-2/3">
                        <div class="relative h-20 w-14 overflow-hidden rounded-xl bg-slate-800/60">
                            @if ($poster)
                                <img src="{{ $poster }}" alt="{{ $title }} poster" class="h-full w-full object-cover" loading="lazy">
                            @else
                                <div class="flex h-full items-center justify-center text-[0.65rem] uppercase tracking-wide text-slate-500">
                                    No artwork
                                </div>
                            @endif
                        </div>
                        <div>
                            <p class="text-[0.7rem] font-semibold uppercase tracking-wide text-emerald-300">{{ $typeLabel }}</p>
                            <h3 class="text-base font-semibold text-slate-100">
                                @if ($route)
                                    <a href="{{ $route }}" class="hover:text-emerald-200">{{ $title }}</a>
                                @else
                                    {{ $title }}
                                @endif
                            </h3>
                            @if ($subtitle)
                                <p class="text-sm text-slate-400">{{ $subtitle }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="lg:w-1/3 lg:text-right">
                        <p class="text-xs uppercase tracking-wide text-slate-500">Watched</p>
                        @if ($watchedAt)
                            <p class="text-sm font-semibold text-slate-200">{{ $watchedAt->toDayDateTimeString() }}</p>
                            <p class="text-xs text-slate-400">{{ $watchedAt->diffForHumans() }}</p>
                        @else
                            <p class="text-sm text-slate-400">Unknown</p>
                        @endif
                    </div>
                </article>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-800/60 bg-slate-950/30 px-6 py-10 text-center text-sm text-slate-400">
                    <p class="font-semibold text-slate-200">No watch history yet</p>
                    <p class="mt-2 text-slate-400">Start streaming titles to populate this feed. We'll track every play for you.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $histories->links() }}
        </div>
    </section>
</div>
