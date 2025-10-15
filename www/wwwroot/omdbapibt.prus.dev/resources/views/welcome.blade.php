<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif

        @livewireStyles
    </head>
    <body class="h-full bg-slate-950 text-slate-100 antialiased">
        <div class="relative flex min-h-screen flex-col lg:flex-row">
            <aside
                id="primary-sidebar"
                data-flux-sidebar
                class="fixed inset-y-0 left-0 z-40 flex w-72 max-w-full -translate-x-full flex-col gap-8 bg-slate-900/95 px-6 py-8 shadow-xl transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 lg:bg-slate-900/80 lg:shadow-none"
                aria-label="Main navigation"
                aria-hidden="true"
            >
                <div class="flex items-center justify-between">
                    <a href="/" class="text-lg font-semibold text-white">Flux Movies</a>
                    <button
                        type="button"
                        class="inline-flex items-center rounded-full border border-white/10 p-2 text-slate-300 transition hover:border-amber-400/60 hover:text-amber-200 lg:hidden"
                        data-flux-sidebar-toggle
                        data-target="primary-sidebar"
                        aria-controls="primary-sidebar"
                        aria-label="Close navigation"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <nav class="space-y-1 text-sm font-medium text-slate-300">
                    <a href="#" class="flex items-center justify-between rounded-xl px-4 py-2 transition hover:bg-slate-800/80 hover:text-white">
                        Browse
                        <span class="rounded-full bg-amber-500/20 px-2 py-0.5 text-xs text-amber-300">New</span>
                    </a>
                    <a href="#" class="block rounded-xl px-4 py-2 transition hover:bg-slate-800/80 hover:text-white">Movies</a>
                    <a href="#" class="block rounded-xl px-4 py-2 transition hover:bg-slate-800/80 hover:text-white">TV Shows</a>
                    <a href="#" class="block rounded-xl px-4 py-2 transition hover:bg-slate-800/80 hover:text-white">People</a>
                    <a href="#" class="block rounded-xl px-4 py-2 transition hover:bg-slate-800/80 hover:text-white">Collections</a>
                </nav>
                <div class="mt-auto space-y-4 rounded-2xl border border-white/10 bg-slate-800/40 p-4 text-sm text-slate-300">
                    <p class="font-semibold text-white">Try Flux Premium</p>
                    <p>Unlock 4K streams, early releases and collaborative watchlists for your friends.</p>
                    <button class="w-full rounded-full bg-amber-500 px-4 py-2 text-sm font-semibold text-slate-950 transition hover:bg-amber-400">Start free trial</button>
                </div>
            </aside>

            <div
                data-flux-sidebar-overlay
                data-target="primary-sidebar"
                class="fixed inset-0 z-30 bg-black/60 opacity-0 transition duration-300 ease-in-out pointer-events-none lg:hidden"
                aria-hidden="true"
            ></div>

            <div class="flex min-h-screen flex-1 flex-col">
                <header class="sticky top-0 z-20 border-b border-white/5 bg-slate-950/80 backdrop-blur">
                    <div class="flex items-center justify-between gap-4 px-6 py-4 sm:px-10">
                        <div class="flex items-center gap-3">
                            <button
                                type="button"
                                class="inline-flex items-center justify-center rounded-full border border-white/10 p-2 text-slate-300 transition hover:border-amber-400/60 hover:text-amber-200 lg:hidden"
                                data-flux-sidebar-toggle
                                data-target="primary-sidebar"
                                aria-controls="primary-sidebar"
                                aria-label="Open navigation"
                            >
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>
                            <div class="relative hidden sm:block">
                                <input
                                    type="search"
                                    placeholder="Search movies, shows, people..."
                                    class="w-64 rounded-full border border-white/10 bg-slate-900/80 px-4 py-2 text-sm text-white placeholder:text-slate-500 focus:border-amber-400/70 focus:outline-none focus:ring-2 focus:ring-amber-400/30"
                                >
                                <svg class="absolute right-4 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35m1.35-4.15a6 6 0 1 1-12 0 6 6 0 0 1 12 0Z" />
                                </svg>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <button class="rounded-full border border-white/10 px-4 py-2 text-sm font-medium text-slate-300 transition hover:border-amber-400/60 hover:text-amber-200">Log in</button>
                            <button class="hidden rounded-full bg-amber-500 px-4 py-2 text-sm font-semibold text-slate-950 transition hover:bg-amber-400 sm:inline-flex">Create account</button>
                        </div>
                    </div>
                </header>

                <main class="flex-1 overflow-y-auto">
                    <section class="space-y-10 px-6 py-10 sm:px-10">
                        <div class="grid gap-10 lg:grid-cols-[minmax(0,1fr)_24rem]">
                            <div class="space-y-6">
                                <div class="space-y-3">
                                    <p class="text-xs uppercase tracking-[0.4em] text-amber-400">Now Streaming</p>
                                    <h1 class="text-4xl font-semibold leading-tight text-white sm:text-5xl">
                                        Your curated movie universe powered by Flux
                                    </h1>
                                    <p class="max-w-2xl text-lg text-slate-300">
                                        Discover cross-network releases, multilingual metadata, and rich watchlists that follow you everywhere. Built with Livewire and Flux, our mobile-first hub keeps the stories you love close at hand.
                                    </p>
                                </div>
                                <div class="flex flex-wrap gap-3 text-sm text-slate-300">
                                    <span class="inline-flex items-center gap-2 rounded-full border border-white/10 px-4 py-2">
                                        <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                                        Fresh drops daily
                                    </span>
                                    <span class="inline-flex items-center gap-2 rounded-full border border-white/10 px-4 py-2">
                                        <span class="h-2 w-2 rounded-full bg-sky-400"></span>
                                        Personalized queues
                                    </span>
                                    <span class="inline-flex items-center gap-2 rounded-full border border-white/10 px-4 py-2">
                                        <span class="h-2 w-2 rounded-full bg-amber-400"></span>
                                        Premium insights
                                    </span>
                                </div>
                                <div class="flex flex-col gap-3 text-sm text-slate-300 sm:flex-row sm:items-center">
                                    <button class="inline-flex items-center justify-center gap-2 rounded-full bg-amber-500 px-6 py-3 text-base font-semibold text-slate-950 shadow-lg shadow-amber-500/30 transition hover:bg-amber-400">
                                        Explore catalog
                                    </button>
                                    <button class="inline-flex items-center justify-center gap-2 rounded-full border border-white/10 px-6 py-3 text-base font-semibold text-white transition hover:border-amber-400/60 hover:text-amber-200">
                                        Watch trailer
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m8 5 11 7-11 7V5Z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div class="relative overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 shadow-xl">
                                <div class="absolute inset-0 bg-gradient-to-b from-amber-400/40 via-transparent to-slate-950/90"></div>
                                <div class="relative aspect-[4/5]">
                                    <div class="absolute inset-0 bg-slate-900/60"></div>
                                    <img
                                        src="https://dummyimage.com/800x1000/1f2937/ffffff&text=Flux+Premieres"
                                        alt="Featured premieres"
                                        loading="lazy"
                                        decoding="async"
                                        class="absolute inset-0 h-full w-full object-cover opacity-0 transition-opacity duration-700"
                                        onload="this.classList.remove('opacity-0');"
                                    >
                                </div>
                                <div class="relative space-y-3 p-6 text-slate-200">
                                    <h2 class="text-2xl font-semibold text-white">Premiere spotlight</h2>
                                    <p>Experience synchronized watch parties, localized metadata, and seamless device handoffs with Flux Premium.</p>
                                    <div class="flex items-center gap-3 text-sm">
                                        <span class="inline-flex items-center gap-2 rounded-full border border-white/10 px-3 py-1">
                                            <svg class="h-4 w-4 text-emerald-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Mobile-first design
                                        </span>
                                        <span class="inline-flex items-center gap-2 rounded-full border border-white/10 px-3 py-1">
                                            <svg class="h-4 w-4 text-sky-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m9 5 7 7-7 7" />
                                            </svg>
                                            Flux sidebar toggles
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="px-6 pb-16 sm:px-10">
                        <livewire:movie-listings />
                    </section>
                </main>

                <footer class="border-t border-white/5 bg-slate-950/80 px-6 py-6 text-sm text-slate-400 sm:px-10">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <p>&copy; {{ date('Y') }} Flux Movies. All rights reserved.</p>
                        <div class="flex flex-wrap gap-4">
                            <a href="#" class="transition hover:text-amber-200">Privacy</a>
                            <a href="#" class="transition hover:text-amber-200">Terms</a>
                            <a href="#" class="transition hover:text-amber-200">Support</a>
                        </div>
                    </div>
                </footer>
            </div>
        </div>

        @livewireScripts
    </body>
</html>
