<div class="space-y-12">
    <div class="rounded-3xl border border-slate-800/60 bg-slate-900/70 p-8">
        <h1 class="text-2xl font-semibold text-white">{{ __('ui.admin.panel.title') }}</h1>
        <p class="mt-2 text-sm text-slate-300">{{ __('ui.admin.panel.subtitle') }}</p>

        <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <a href="#movies" class="group flex items-center justify-between rounded-2xl border border-slate-800/80 bg-slate-950/60 px-4 py-3 text-sm text-slate-200 transition hover:border-emerald-400 hover:text-emerald-200">
                <span>{{ __('ui.admin.panel.sections.movies.nav') }}</span>
                <span aria-hidden="true" class="text-emerald-400 transition group-hover:translate-x-1">→</span>
            </a>
            <a href="#tv-shows" class="group flex items-center justify-between rounded-2xl border border-slate-800/80 bg-slate-950/60 px-4 py-3 text-sm text-slate-200 transition hover:border-emerald-400 hover:text-emerald-200">
                <span>{{ __('ui.admin.panel.sections.tv_shows.nav') }}</span>
                <span aria-hidden="true" class="text-emerald-400 transition group-hover:translate-x-1">→</span>
            </a>
            <a href="#people" class="group flex items-center justify-between rounded-2xl border border-slate-800/80 bg-slate-950/60 px-4 py-3 text-sm text-slate-200 transition hover:border-emerald-400 hover:text-emerald-200">
                <span>{{ __('ui.admin.panel.sections.people.nav') }}</span>
                <span aria-hidden="true" class="text-emerald-400 transition group-hover:translate-x-1">→</span>
            </a>
            <a href="#genres" class="group flex items-center justify-between rounded-2xl border border-slate-800/80 bg-slate-950/60 px-4 py-3 text-sm text-slate-200 transition hover:border-emerald-400 hover:text-emerald-200">
                <span>{{ __('ui.admin.panel.sections.genres.nav') }}</span>
                <span aria-hidden="true" class="text-emerald-400 transition group-hover:translate-x-1">→</span>
            </a>
            <a href="#languages" class="group flex items-center justify-between rounded-2xl border border-slate-800/80 bg-slate-950/60 px-4 py-3 text-sm text-slate-200 transition hover:border-emerald-400 hover:text-emerald-200">
                <span>{{ __('ui.admin.panel.sections.languages.nav') }}</span>
                <span aria-hidden="true" class="text-emerald-400 transition group-hover:translate-x-1">→</span>
            </a>
            <a href="#countries" class="group flex items-center justify-between rounded-2xl border border-slate-800/80 bg-slate-950/60 px-4 py-3 text-sm text-slate-200 transition hover:border-emerald-400 hover:text-emerald-200">
                <span>{{ __('ui.admin.panel.sections.countries.nav') }}</span>
                <span aria-hidden="true" class="text-emerald-400 transition group-hover:translate-x-1">→</span>
            </a>
        </div>
    </div>

    <section id="movies" aria-labelledby="movies-heading" class="space-y-6">
        <h2 id="movies-heading" class="text-xl font-semibold text-white">{{ __('ui.admin.panel.sections.movies.heading') }}</h2>
        <livewire:admin.crud.manage-movies :key="'movies-manager'" />
    </section>

    <section id="tv-shows" aria-labelledby="tv-heading" class="space-y-6">
        <h2 id="tv-heading" class="text-xl font-semibold text-white">{{ __('ui.admin.panel.sections.tv_shows.heading') }}</h2>
        <livewire:admin.crud.manage-tv-shows :key="'tv-shows-manager'" />
    </section>

    <section id="people" aria-labelledby="people-heading" class="space-y-6">
        <h2 id="people-heading" class="text-xl font-semibold text-white">{{ __('ui.admin.panel.sections.people.heading') }}</h2>
        <livewire:admin.crud.manage-people :key="'people-manager'" />
    </section>

    <section id="genres" aria-labelledby="genres-heading" class="space-y-6">
        <h2 id="genres-heading" class="text-xl font-semibold text-white">{{ __('ui.admin.panel.sections.genres.heading') }}</h2>
        <livewire:admin.crud.manage-genres :key="'genres-manager'" />
    </section>

    <section id="languages" aria-labelledby="languages-heading" class="space-y-6">
        <h2 id="languages-heading" class="text-xl font-semibold text-white">{{ __('ui.admin.panel.sections.languages.heading') }}</h2>
        <livewire:admin.crud.manage-languages :key="'languages-manager'" />
    </section>

    <section id="countries" aria-labelledby="countries-heading" class="space-y-6">
        <h2 id="countries-heading" class="text-xl font-semibold text-white">{{ __('ui.admin.panel.sections.countries.heading') }}</h2>
        <livewire:admin.crud.manage-countries :key="'countries-manager'" />
    </section>
</div>
