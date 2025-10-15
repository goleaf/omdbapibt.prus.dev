@php
    $locale = app()->getLocale();
@endphp

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-white">Curated recommendations</h2>
        <button
            type="button"
            wire:click="refreshRecommendations"
            class="inline-flex items-center gap-2 rounded-full border border-slate-700 px-3 py-1.5 text-xs font-medium text-slate-200 transition hover:border-emerald-400 hover:text-emerald-200"
        >
            <span>Refresh</span>
        </button>
    </div>

    @if (! $this->isHydrated)
        <div class="grid gap-4 md:grid-cols-2">
            @for ($i = 0; $i < 4; $i++)
                <div class="h-32 animate-pulse rounded-2xl bg-slate-800/40"></div>
            @endfor
        </div>
    @elseif ($this->hasRecommendations())
        <div class="grid gap-6 lg:grid-cols-2">
            @foreach ($items as $item)
                <x-flux.card :heading="$item['title']" :subheading="$item['tagline']">
                    <div class="flex flex-wrap items-center gap-3 text-sm text-slate-300">
                        @if ($item['vote_average'])
                            <x-flux.rating-badge :score="$item['vote_average']" label="Score" />
                        @endif
                        @if ($item['genres'])
                            <span class="rounded-full border border-slate-700/60 px-3 py-1 text-xs uppercase tracking-[0.35em] text-slate-300">{{ $item['genres'] }}</span>
                        @endif
                        @if ($item['popularity'])
                            <span class="text-xs text-slate-400">Popularity {{ number_format((float) $item['popularity'], 1) }}</span>
                        @endif
                    </div>

                    <div class="mt-4 flex items-center gap-3">
                        <flux:button href="{{ route('movies.show', ['locale' => $locale, 'movie' => $item['slug']]) }}" variant="primary" color="emerald" size="sm">
                            View detail
                        </flux:button>
                    </div>
                </x-flux.card>
            @endforeach
        </div>
    @else
        <div class="rounded-2xl border border-dashed border-slate-700/60 bg-slate-900/60 p-6 text-sm text-slate-300">
            Track a few movies first and we will begin shaping bespoke recommendations for you.
        </div>
    @endif
</div>
