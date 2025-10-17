<div class="space-y-10">
    @if ($locked)
        <section class="mx-auto max-w-3xl space-y-6 rounded-2xl border border-slate-800 bg-slate-900/80 p-8">
            <div class="space-y-2 text-left">
                <h2 class="text-2xl font-semibold text-white">{{ __('Subscription required') }}</h2>
                <p class="text-sm text-slate-300">
                    {{ __('This section is only for subscribers. Sign in or upgrade to browse every title and use the full set of tools.') }}
                </p>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row">
                <a href="{{ localized_route('login') }}" class="rounded-full border border-slate-700 px-6 py-2 text-sm font-semibold text-slate-100 transition hover:border-emerald-400 hover:text-emerald-200">
                    {{ __('Sign in') }}
                </a>
                <a href="{{ localized_route('register') }}" class="rounded-full bg-emerald-500 px-6 py-2 text-sm font-semibold text-emerald-950 transition hover:bg-emerald-400">
                    {{ __('Start a subscription') }}
                </a>
                <a href="{{ localized_route('pricing') }}" class="rounded-full border border-emerald-400/60 px-6 py-2 text-sm font-semibold text-emerald-200 transition hover:border-emerald-300">
                    {{ __('See plans') }}
                </a>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="space-y-3 rounded-xl border border-slate-800 bg-slate-950/70 p-5 text-left">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-400">{{ __('What changes with access') }}</h3>
                    <ul class="space-y-2 text-sm text-slate-300">
                        <li>{{ __('Search the entire catalog without limits') }}</li>
                        <li>{{ __('Save filters that fit how you watch') }}</li>
                        <li>{{ __('Pick up where you left off on any device') }}</li>
                    </ul>
                </div>
                <div class="space-y-3 rounded-xl border border-slate-800 bg-slate-950/70 p-5 text-left">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-400">{{ __('Already a member?') }}</h3>
                    <p class="text-sm text-slate-300">
                        {{ __('Head to checkout to confirm your billing details and we will unlock the page right away.') }}
                    </p>
                    <a href="{{ localized_route('checkout') }}" class="inline-flex w-max rounded-full bg-emerald-500 px-4 py-2 text-sm font-semibold text-emerald-950 transition hover:bg-emerald-400">
                        {{ __('Go to checkout') }}
                    </a>
                </div>
            </div>
        </section>
    @else
        <div class="grid gap-8 lg:grid-cols-[280px,1fr]">
            <aside class="space-y-6">
                <section class="space-y-4 rounded-2xl border border-slate-800 bg-slate-900/80 p-5">
                    <div class="space-y-1">
                        <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-400">{{ __('Filters') }}</h2>
                        <p class="text-sm text-slate-300">{{ __('Pick a year, genre, or streaming provider to tighten the results.') }}</p>
                    </div>
                    <div class="pt-1">
                        @livewire('media-filters')
                    </div>
                </section>

                <section class="space-y-3 rounded-2xl border border-slate-800 bg-slate-900/80 p-5">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-400">{{ __('Quick groups') }}</h3>
                    <ul class="space-y-2 text-sm text-slate-300">
                        <li class="rounded-xl border border-slate-800 bg-slate-950/80 px-4 py-2">{{ __('Now playing in theaters') }}</li>
                        <li class="rounded-xl border border-slate-800 bg-slate-950/80 px-4 py-2">{{ __('Highly rated classics') }}</li>
                        <li class="rounded-xl border border-slate-800 bg-slate-950/80 px-4 py-2">{{ __('Family night picks') }}</li>
                    </ul>
                </section>
            </aside>

            <section class="space-y-8">
                <article class="space-y-4 rounded-2xl border border-slate-800 bg-slate-900/80 p-6">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-xl font-semibold text-slate-50">{{ __('Trending right now') }}</h2>
                            <p class="text-sm text-slate-300">{{ __('Fresh picks updated every hour from OMDb and TMDb data.') }}</p>
                        </div>
                        <div class="flex gap-3">
                            <button class="rounded-full border border-slate-700 px-4 py-2 text-sm text-slate-200 transition hover:border-emerald-400 hover:text-emerald-200">
                                {{ __('Save filters') }}
                            </button>
                            <button class="rounded-full bg-emerald-500 px-4 py-2 text-sm font-semibold text-emerald-950 transition hover:bg-emerald-400">
                                {{ __('Shuffle list') }}
                            </button>
                        </div>
                    </div>

                    <div class="space-y-6">
                        @livewire('trending-reel')
                    </div>
                </article>

                <article class="space-y-4 rounded-2xl border border-slate-800 bg-slate-900/80 p-6">
                    <div class="space-y-2">
                        <h3 class="text-lg font-semibold text-slate-50">{{ __('Need inspiration?') }}</h3>
                        <p class="text-sm text-slate-300">{{ __('Browse a few ready-made collections for nights when you just want something reliable.') }}</p>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ([
                            __('Documentaries with buzz'),
                            __('Comfort rewatches'),
                            __('Festival favorites'),
                            __('Short series to finish fast'),
                            __('Movies under two hours'),
                            __('Kid friendly adventures'),
                        ] as $collection)
                            <a href="#" class="rounded-xl border border-slate-800 bg-slate-950/70 p-4 text-sm text-slate-200 transition hover:border-emerald-400/60">
                                <span class="font-semibold text-slate-50">{{ $collection }}</span>
                                <p class="mt-2 text-xs text-slate-400">{{ __('Open the list to see everything inside.') }}</p>
                            </a>
                        @endforeach
                    </div>
                </article>
            </section>
        </div>
    @endif
</div>
