<div class="space-y-12">
    <div class="grid gap-10 lg:grid-cols-[320px,1fr] lg:items-start">
        <div class="space-y-6">
            <div class="overflow-hidden rounded-3xl border border-slate-800/60">
                <img src="{{ $profileImage }}" alt="{{ __('ui.people.profile_alt', ['name' => $personModel->name]) }}" class="h-full w-full object-cover" />
            </div>
            <div class="overflow-hidden rounded-3xl border border-slate-800/60">
                <img src="{{ $posterImage }}" alt="{{ __('ui.people.poster_alt', ['name' => $personModel->name]) }}" class="h-full w-full object-cover" />
            </div>
            <div class="rounded-3xl border border-slate-800/60 bg-slate-900/70 p-6 text-sm text-slate-300">
                <p class="text-xs uppercase tracking-[0.35em] text-emerald-200">{{ __('ui.people.vitals_heading') }}</p>
                <dl class="mt-4 space-y-2">
                    <div class="flex items-center justify-between">
                        <dt>{{ __('ui.people.born_label') }}</dt>
                        <dd>{{ optional($personModel->birthday)->toFormattedDateString() ?? '—' }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt>{{ __('ui.people.place_label') }}</dt>
                        <dd>{{ $personModel->place_of_birth ?? '—' }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt>{{ __('ui.people.known_for_label') }}</dt>
                        <dd>{{ $personModel->known_for_department ?? '—' }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt>{{ __('ui.people.popularity_label') }}</dt>
                        <dd>{{ number_format((float) $personModel->popularity, 1) }}</dd>
                    </div>
                </dl>
            </div>
        </div>
        <div class="space-y-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-semibold text-white">{{ $personModel->name }}</h1>
                    <p class="text-sm text-slate-300">{{ $personModel->known_for_department }}</p>
                </div>
            </div>
            <div class="rounded-3xl border border-slate-800/60 bg-slate-900/70 p-6">
                <div class="space-y-4">
                    <h2 class="text-xs uppercase tracking-[0.35em] text-emerald-200">{{ __('ui.people.biography_heading') }}</h2>
                    <p class="text-sm leading-relaxed text-slate-200 whitespace-pre-line">{{ $biography }}</p>
                </div>
            </div>

            @foreach ($credits as $groups)
                <x-flux.card :heading="__('ui.people.credits_heading', ['type' => $groups['label']])">
                    <div class="space-y-4">
                        @if (! empty($groups['movies'] ?? []))
                            <div>
                                <h3 class="text-sm font-semibold text-white">{{ __('ui.people.movies_heading') }}</h3>
                                <ul class="mt-3 space-y-2 text-sm text-slate-300">
                                    @foreach ($groups['movies'] as $credit)
                                        <li class="flex items-center justify-between">
                                            <a href="{{ route('movies.show', ['locale' => app()->getLocale(), 'movie' => $credit['slug']]) }}" class="text-emerald-300 hover:text-emerald-200">{{ $credit['title'] }}</a>
                                            <span class="text-xs text-slate-400">{{ $credit['role'] }} · {{ $credit['year'] ?? '—' }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if (! empty($groups['shows'] ?? []))
                            <div>
                                <h3 class="text-sm font-semibold text-white">{{ __('ui.people.tv_heading') }}</h3>
                                <ul class="mt-3 space-y-2 text-sm text-slate-300">
                                    @foreach ($groups['shows'] as $credit)
                                        <li class="flex items-center justify-between">
                                            <a href="{{ route('shows.show', ['locale' => app()->getLocale(), 'slug' => $credit['slug']]) }}" class="text-emerald-300 hover:text-emerald-200">{{ $credit['title'] }}</a>
                                            <span class="text-xs text-slate-400">{{ $credit['role'] }} · {{ $credit['year'] ?? '—' }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </x-flux.card>
            @endforeach
        </div>
    </div>
</div>
