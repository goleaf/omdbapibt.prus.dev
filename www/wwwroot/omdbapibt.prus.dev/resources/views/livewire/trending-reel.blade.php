<section aria-labelledby="trending-heading" class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 id="trending-heading" class="text-xl font-semibold tracking-tight text-slate-50">Trending now</h2>
        <a href="{{ route('browse') }}" class="text-sm font-medium text-emerald-400 transition hover:text-emerald-300">See all</a>
    </div>

    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ($this->items as $item)
            <article class="group relative overflow-hidden rounded-3xl border border-slate-800 bg-slate-900/60 shadow-lg shadow-slate-950/40 transition hover:border-emerald-400/60 hover:shadow-emerald-500/20">
                <img src="{{ $item['poster'] }}" alt="{{ $item['title'] }} poster"
                    class="h-60 w-full object-cover transition duration-500 group-hover:scale-105">
                <div class="space-y-2 p-5">
                    <div class="flex items-center justify-between text-xs uppercase tracking-wide text-slate-400">
                        <span>{{ $item['media_type'] }}</span>
                        <span class="flex items-center gap-1 font-semibold text-amber-300">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4">
                                <path fill-rule="evenodd" d="M12 17.27 18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" clip-rule="evenodd" />
                            </svg>
                            {{ number_format($item['rating'], 1) }}
                        </span>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-100">{{ $item['title'] }}</h3>
                    <p class="text-sm text-slate-400">{{ implode(' • ', $item['genres']) }}</p>
                </div>
            </article>
        @endforeach
    </div>
</section>
