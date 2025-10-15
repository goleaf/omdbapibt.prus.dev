<section class="space-y-6">
    <div>
        <h2 class="text-xl font-semibold text-slate-50">{{ __('ui.filters.heading') }}</h2>
        <p class="mt-1 text-sm text-slate-400">{{ __('ui.filters.description') }}</p>
    </div>

    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        <div class="rounded-2xl border border-slate-800 bg-slate-900/60 p-5">
            <label class="text-sm font-semibold text-slate-200">{{ __('ui.filters.type_label') }}</label>
            <div class="mt-3 flex gap-3">
                @foreach (['movies' => __('ui.filters.types.movies'), 'shows' => __('ui.filters.types.shows')] as $value => $label)
                    <button type="button"
                        class="flex-1 rounded-xl border px-4 py-2 text-sm font-medium transition {{ $selected['type'] === $value ? 'border-emerald-400 bg-emerald-500/10 text-emerald-200' : 'border-slate-700 text-slate-300 hover:border-emerald-400/50 hover:text-emerald-200' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/60 p-5">
            <label class="text-sm font-semibold text-slate-200" for="genre">{{ __('ui.filters.genre_label') }}</label>
            <select id="genre" class="mt-3 w-full rounded-xl border border-slate-700 bg-slate-950/60 px-4 py-2 text-sm text-slate-200 focus:border-emerald-400 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                @foreach ($this->genres as $genre)
                    <option @selected($genre === $selected['genre'])>{{ $genre }}</option>
                @endforeach
            </select>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/60 p-5">
            <label class="text-sm font-semibold text-slate-200" for="year">{{ __('ui.filters.year_label') }}</label>
            <select id="year" class="mt-3 w-full rounded-xl border border-slate-700 bg-slate-950/60 px-4 py-2 text-sm text-slate-200 focus:border-emerald-400 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                @foreach ($this->years as $year)
                    <option @selected($year === $selected['year'])>{{ $year }}</option>
                @endforeach
            </select>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/60 p-5">
            <label class="text-sm font-semibold text-slate-200" for="language">{{ __('ui.filters.language_label') }}</label>
            <select id="language" class="mt-3 w-full rounded-xl border border-slate-700 bg-slate-950/60 px-4 py-2 text-sm text-slate-200 focus:border-emerald-400 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                @foreach ($this->languages as $language)
                    <option @selected($language === $selected['language'])>{{ $language }}</option>
                @endforeach
            </select>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/60 p-5">
            <label class="text-sm font-semibold text-slate-200" for="sort">{{ __('ui.filters.sort_label') }}</label>
            <select id="sort" class="mt-3 w-full rounded-xl border border-slate-700 bg-slate-950/60 px-4 py-2 text-sm text-slate-200 focus:border-emerald-400 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                <option value="popularity.desc" selected>{{ __('ui.filters.sort_options.popularity_desc') }}</option>
                <option value="vote_average.desc">{{ __('ui.filters.sort_options.vote_average_desc') }}</option>
                <option value="release_date.desc">{{ __('ui.filters.sort_options.release_date_desc') }}</option>
                <option value="release_date.asc">{{ __('ui.filters.sort_options.release_date_asc') }}</option>
            </select>
        </div>

        <div class="flex items-end justify-between rounded-2xl border border-slate-800 bg-slate-900/60 p-5">
            <div>
                <h3 class="text-sm font-semibold text-slate-200">{{ __('ui.filters.results_title') }}</h3>
                <p class="mt-1 text-xs text-slate-400">{{ __('ui.filters.results_summary', [
                    'genre' => $selected['genre'],
                    'type' => __('ui.filters.types.' . $selected['type']),
                    'year' => $selected['year'],
                ]) }}</p>
            </div>
            <button type="button" class="rounded-full bg-emerald-500 px-6 py-2 text-sm font-semibold text-emerald-950 transition hover:bg-emerald-400">
                {{ __('ui.filters.apply') }}
            </button>
        </div>
    </div>
</section>
