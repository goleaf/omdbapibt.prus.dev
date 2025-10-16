@php
    $locale = app()->getLocale();
@endphp

<div>
    @if ($toggleMode)
        <div class="flex flex-wrap items-center gap-3">
            @if (! $isAuthenticated)
                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 rounded-md bg-slate-800 px-4 py-2 text-sm font-semibold text-slate-100 transition hover:bg-slate-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-500">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-9A2.25 2.25 0 0 0 2.25 5.25v13.5A2.25 2.25 0 0 0 4.5 21h9a2.25 2.25 0 0 0 2.25-2.25V15" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 12h-7.5" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="m15 15 3-3-3-3" />
                    </svg>
                    {{ __('Sign in to save') }}
                </a>
            @else
                <button type="button" wire:click="toggle" wire:loading.attr="disabled" wire:target="toggle" class="inline-flex items-center gap-2 rounded-md border border-slate-600 px-4 py-2 text-sm font-semibold text-slate-100 transition hover:border-emerald-400 hover:text-emerald-300 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-400">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        @if ($isSaved)
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        @else
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6" />
                        @endif
                    </svg>
                    <span>
                        {{ $isSaved ? __('Remove from watchlist') : __('Add to watchlist') }}
                    </span>
                </button>
            @endif
        </div>
    @else
        <section class="space-y-6 rounded-2xl border border-slate-200 bg-white p-6 shadow">
            <header class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-slate-900">{{ __('Your watchlist') }}</h2>
                    <p class="text-sm text-slate-500">{{ __('Keep track of movies and TV series you want to explore later.') }}</p>
                </div>
                <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-slate-600">
                    {{ trans_choice(':count title saved|:count titles saved', count($items['movies']) + count($items['shows']), ['count' => count($items['movies']) + count($items['shows'])]) }}
                </span>
            </header>

            @if (! $isAuthenticated)
                <p class="rounded-lg border border-dashed border-slate-300 bg-slate-50 px-4 py-3 text-sm text-slate-600">
                    {{ __('Sign in to start curating your personal watchlist across all your devices.') }}
                </p>
            @elseif (empty($items['movies']) && empty($items['shows']))
                <p class="rounded-lg border border-dashed border-emerald-200 bg-emerald-50 px-4 py-4 text-sm text-emerald-800">
                    {{ __('Your watchlist is empty. Browse movies and shows to add something that inspires you.') }}
                </p>
            @else
                <div class="space-y-8">
                    @if (! empty($items['movies']))
                        <div class="space-y-3">
                            <h3 class="text-lg font-semibold text-slate-900">{{ __('Movies') }}</h3>
                            <ul class="grid gap-4 sm:grid-cols-2">
                                @foreach ($items['movies'] as $movie)
                                    <li wire:key="watchlist-movie-{{ $movie['id'] }}" class="flex items-center justify-between gap-4 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                                        <div>
                                            <a
                                                href="{{ $movie['slug'] ? route('movies.show', ['locale' => app()->getLocale(), 'movie' => $movie['slug']]) : '#' }}"
                                                class="text-sm font-semibold text-slate-900 hover:text-emerald-600"
                                            >
                                                {{ $movie['title'] }}
                                            </a>
                                            <p class="text-xs text-slate-500">
                                                {{ $movie['year'] ? __('Released :year', ['year' => $movie['year']]) : __('Release year unknown') }}
                                            </p>
                                        </div>
                                        <button type="button" wire:click="remove('movie', {{ $movie['id'] }})" wire:loading.attr="disabled" wire:target="remove"
                                            class="inline-flex items-center gap-1 rounded-md border border-transparent bg-slate-900 px-3 py-1 text-xs font-semibold text-white transition hover:bg-slate-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-500">
                                            {{ __('Remove') }}
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (! empty($items['shows']))
                        <div class="space-y-3">
                            <h3 class="text-lg font-semibold text-slate-900">{{ __('TV Shows') }}</h3>
                            <ul class="grid gap-4 sm:grid-cols-2">
                                @foreach ($items['shows'] as $show)
                                    <li wire:key="watchlist-show-{{ $show['id'] }}" class="flex items-center justify-between gap-4 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                                        <div>
                                            <a
                                                href="{{ $show['slug'] ? route('shows.show', ['locale' => app()->getLocale(), 'slug' => $show['slug']]) : '#' }}"
                                                class="text-sm font-semibold text-slate-900 hover:text-emerald-600"
                                            >
                                                {{ $show['title'] }}
                                            </a>
                                            <p class="text-xs text-slate-500">
                                                {{ $show['year'] ? __('First aired :year', ['year' => $show['year']]) : __('First air year unknown') }}
                                            </p>
                                        </div>
                                        <button type="button" wire:click="remove('tv', {{ $show['id'] }})" wire:loading.attr="disabled" wire:target="remove"
                                            class="inline-flex items-center gap-1 rounded-md border border-transparent bg-slate-900 px-3 py-1 text-xs font-semibold text-white transition hover:bg-slate-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-500">
                                            {{ __('Remove') }}
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            @endif
        </section>
    @endif
</div>
