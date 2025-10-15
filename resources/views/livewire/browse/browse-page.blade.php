<div>
    @if ($locked)
        <div class="mx-auto max-w-3xl space-y-8 rounded-3xl border border-slate-800/60 bg-slate-900/70 p-10 text-center">
            <div class="space-y-3">
                <h2 class="text-2xl font-semibold text-white">{{ __('subscriptions.errors.access_required') }}</h2>
                <p class="text-sm text-slate-300">
                    {{ __('Access to advanced filters, watch history sync, and cinematic recommendations is reserved for subscribers.') }}
                </p>
            </div>

            <div class="flex flex-col justify-center gap-3 sm:flex-row">
                <a href="{{ route('login') }}" class="rounded-full border border-slate-700 px-6 py-2 text-sm font-semibold text-slate-100 transition hover:border-emerald-400 hover:text-emerald-200">
                    {{ __('Sign in to continue') }}
                </a>
                <a href="{{ route('register') }}" class="rounded-full bg-emerald-500 px-6 py-2 text-sm font-semibold text-emerald-950 transition hover:bg-emerald-400">
                    {{ __('Create an account') }}
                </a>
                <a href="{{ route('pricing') }}" class="rounded-full border border-emerald-400/50 px-6 py-2 text-sm font-semibold text-emerald-200 transition hover:border-emerald-300">
                    {{ __('Compare plans') }}
                </a>
            </div>

            <div class="grid gap-6 rounded-2xl border border-slate-800/70 bg-slate-950/70 p-6 text-left sm:grid-cols-3">
                <div>
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-400">{{ __('What you get') }}</h3>
                    <ul class="mt-3 space-y-2 text-sm text-slate-300">
                        <li>{{ __('Unlimited HD trailers and streaming providers') }}</li>
                        <li>{{ __('Personalized watchlist and alerts') }}</li>
                        <li>{{ __('Early access to parser updates') }}</li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-400">{{ __('Stay in the loop') }}</h3>
                    <p class="mt-3 text-sm text-slate-300">
                        {{ __('Subscribers are the first to see new parser drops, curated events, and festival spotlights.') }}
                    </p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-400">{{ __('Already subscribed?') }}</h3>
                    <p class="mt-3 text-sm text-slate-300">
                        {{ __('Visit checkout to finalize billing and unlock access immediately.') }}
                    </p>
                    <a href="{{ route('checkout') }}" class="mt-3 inline-flex rounded-full bg-emerald-500 px-4 py-2 text-sm font-semibold text-emerald-950 transition hover:bg-emerald-400">
                        {{ __('Go to checkout') }}
                    </a>
                </div>
            </div>
        </div>
    @else
        <div class="grid gap-10 lg:grid-cols-[320px,1fr]">
            <aside class="space-y-6">
                <div class="rounded-3xl border border-slate-800 bg-slate-900/70 p-6">
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-400">Filters</h2>
                    @livewire('media-filters')
                </div>
                <div class="rounded-3xl border border-slate-800 bg-slate-900/70 p-6">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-400">Curated hubs</h3>
                    <ul class="mt-3 space-y-2 text-sm text-slate-300">
                        <li class="rounded-2xl border border-slate-800 bg-slate-950/80 px-4 py-2">Award winners</li>
                        <li class="rounded-2xl border border-slate-800 bg-slate-950/80 px-4 py-2">Critics choice</li>
                        <li class="rounded-2xl border border-slate-800 bg-slate-950/80 px-4 py-2">Coming soon</li>
                    </ul>
                </div>
            </aside>

            <section class="space-y-8">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-2xl font-semibold text-slate-50">Trending right now</h2>
                        <p class="text-sm text-slate-400">Updated hourly based on TMDb popularity and OMDb ratings.</p>
                    </div>
                    <div class="flex gap-3">
                        <button class="rounded-full border border-slate-700 px-4 py-2 text-sm text-slate-200 transition hover:border-emerald-400 hover:text-emerald-200">Save filters</button>
                        <button class="rounded-full bg-emerald-500 px-4 py-2 text-sm font-semibold text-emerald-950 transition hover:bg-emerald-400">Randomize</button>
                    </div>
                </div>

                @livewire('trending-reel')

                <div class="space-y-6">
                    <h3 class="text-xl font-semibold text-slate-50">More to explore</h3>
                    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach (['Neo Noir', 'Time Travel', 'Global Cinema', 'Documentary Spotlight', 'Family Favorites', 'Hidden Gems'] as $collection)
                            <a href="#" class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5 text-sm text-slate-200 transition hover:border-emerald-400/60">
                                <span class="font-semibold text-slate-50">{{ $collection }}</span>
                                <p class="mt-2 text-xs text-slate-400">Dive into our hand-picked {{ strtolower($collection) }} selections.</p>
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
        </div>
    @endif
</div>
