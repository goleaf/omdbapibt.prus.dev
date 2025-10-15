@php use Illuminate\Support\Str; @endphp

<div class="space-y-10">
    <section class="rounded-2xl border border-slate-800 bg-slate-900/60 p-6 shadow-xl shadow-slate-950/20 backdrop-blur">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-2xl font-semibold text-slate-100">Browse TV Shows</h2>
                <p class="mt-1 text-sm text-slate-400">
                    Fine-tune the library by choosing a genre, on-air status, original language, or popularity band.
                </p>
            </div>
            <div class="flex items-center gap-2 text-sm text-slate-400">
                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-amber-500/10 text-amber-400">{{ count($shows) }}</span>
                <span class="font-medium">matching titles</span>
            </div>
        </div>

        <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <label class="flex flex-col gap-2 text-sm text-slate-300">
                <span class="font-semibold text-slate-200">Genre</span>
                <select
                    wire:model.live="filters.genre"
                    class="w-full rounded-xl border border-slate-700 bg-slate-950/60 px-4 py-2 text-slate-100 shadow-inner shadow-slate-950/40 focus:border-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-500/40"
                >
                    <option value="all">All genres</option>
                    @foreach ($genres as $genre)
                        <option value="{{ $genre }}">{{ $genre }}</option>
                    @endforeach
                </select>
            </label>

            <label class="flex flex-col gap-2 text-sm text-slate-300">
                <span class="font-semibold text-slate-200">Status</span>
                <select
                    wire:model.live="filters.status"
                    class="w-full rounded-xl border border-slate-700 bg-slate-950/60 px-4 py-2 text-slate-100 shadow-inner shadow-slate-950/40 focus:border-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-500/40"
                >
                    <option value="all">All statuses</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status }}">{{ $status }}</option>
                    @endforeach
                </select>
            </label>

            <label class="flex flex-col gap-2 text-sm text-slate-300">
                <span class="font-semibold text-slate-200">Language</span>
                <select
                    wire:model.live="filters.language"
                    class="w-full rounded-xl border border-slate-700 bg-slate-950/60 px-4 py-2 text-slate-100 shadow-inner shadow-slate-950/40 focus:border-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-500/40"
                >
                    <option value="all">All languages</option>
                    @foreach ($languages as $language)
                        <option value="{{ $language }}">{{ $language }}</option>
                    @endforeach
                </select>
            </label>

            <label class="flex flex-col gap-2 text-sm text-slate-300">
                <span class="font-semibold text-slate-200">Popularity</span>
                <select
                    wire:model.live="filters.popularity"
                    class="w-full rounded-xl border border-slate-700 bg-slate-950/60 px-4 py-2 text-slate-100 shadow-inner shadow-slate-950/40 focus:border-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-500/40"
                >
                    @foreach ($popularityRanges as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </label>
        </div>
    </section>

    <section class="grid gap-6 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4">
        @forelse ($shows as $show)
            <article
                wire:key="tv-card-{{ Str::slug($show['name']) }}"
                class="group relative flex h-full flex-col overflow-hidden rounded-2xl border border-slate-800 bg-slate-900/70 shadow-lg shadow-slate-950/30 transition duration-200 ease-out hover:-translate-y-1 hover:border-amber-500/70 hover:shadow-amber-500/10"
            >
                <div class="relative aspect-[2/3] w-full overflow-hidden">
                    <img
                        src="{{ $show['poster'] }}"
                        alt="Poster for {{ $show['name'] }}"
                        class="h-full w-full object-cover transition duration-300 ease-out group-hover:scale-105 group-hover:brightness-110"
                    >
                    <div class="pointer-events-none absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-slate-950/95 to-slate-950/0"></div>
                    <div class="absolute bottom-3 left-3 flex items-center gap-2 text-[0.65rem] font-semibold uppercase tracking-wide">
                        <span class="rounded-full bg-amber-500/90 px-2 py-1 text-slate-950">{{ number_format($show['rating'], 1) }} ★</span>
                        <span class="rounded-full bg-slate-900/80 px-2 py-1 text-slate-200/90">{{ $show['status'] }}</span>
                    </div>
                </div>

                <div class="flex flex-1 flex-col gap-4 p-5">
                    <header class="space-y-1">
                        <h3 class="text-lg font-semibold text-slate-100">{{ $show['name'] }}</h3>
                        <p class="text-xs uppercase tracking-wide text-slate-400">
                            {{ $show['genre'] }} • {{ $show['language'] }} • {{ $show['first_air_date'] }}
                        </p>
                    </header>

                    <p class="text-sm text-slate-300/90">
                        {{ Str::limit($show['overview'], 140) }}
                    </p>

                    <footer class="mt-auto flex items-center justify-between text-xs text-slate-400">
                        <div class="flex items-center gap-3">
                            <span class="flex items-center gap-1 text-amber-300">
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path d="M10 2.5l1.902 4.54 4.852.42-3.67 3.144 1.113 4.796L10 12.93l-4.197 2.47 1.113-4.796-3.67-3.144 4.852-.42L10 2.5z" />
                                </svg>
                                {{ number_format($show['popularity'], 1) }}
                            </span>
                            <span class="h-1 w-1 rounded-full bg-slate-600"></span>
                            <span>{{ $show['seasons'] }} {{ Str::plural('Season', $show['seasons']) }}</span>
                        </div>
                        <button
                            type="button"
                            class="inline-flex items-center gap-1 rounded-full border border-amber-500/50 bg-amber-500/10 px-3 py-1 font-medium text-amber-300 transition hover:bg-amber-500/20"
                        >
                            View details
                            <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M6.22 4.22a.75.75 0 011.06 0L13 9.94a.75.75 0 010 1.06l-5.72 5.72a.75.75 0 01-1.06-1.06L11.44 11 6.22 5.78a.75.75 0 010-1.06z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </footer>
                </div>
            </article>
        @empty
            <div class="col-span-full rounded-2xl border border-slate-800 bg-slate-900/60 p-10 text-center">
                <h3 class="text-lg font-semibold text-slate-100">No shows match your filters yet.</h3>
                <p class="mt-2 text-sm text-slate-400">Try broadening your selection to discover more series.</p>
            </div>
        @endforelse
    </section>
</div>
