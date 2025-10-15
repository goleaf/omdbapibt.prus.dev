<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name', 'OMDb API BT') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link rel="preload" as="style" href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700">
    <link rel="stylesheet" href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-full bg-transparent text-slate-900 antialiased transition-colors duration-500 dark:text-slate-100">
    <div class="relative flex min-h-screen flex-col">
        <header class="sticky top-0 z-40 border-b border-white/20 bg-[color:var(--surface-layer)]/85 backdrop-blur-xl transition dark:border-white/10">
            <div class="flux-container flex h-16 items-center justify-between gap-4 sm:h-20">
                <a href="{{ route('home') }}" class="group flex items-center gap-3 text-sm font-semibold uppercase tracking-[0.35em] text-slate-700 transition hover:text-emerald-500 dark:text-slate-200 dark:hover:text-emerald-200">
                    <span class="flex size-9 items-center justify-center rounded-full bg-gradient-to-br from-emerald-400/80 via-cyan-400/70 to-blue-500/70 text-slate-900 shadow-[var(--shadow-glow)] transition group-hover:scale-105">
                        OM
                    </span>
                    <span class="hidden sm:inline">Flux</span>
                </a>

                <nav class="hidden items-center gap-8 text-xs font-semibold uppercase tracking-[0.28em] text-slate-600 md:flex dark:text-slate-300">
                    <a href="{{ route('home') }}" class="transition hover:text-emerald-500 dark:hover:text-emerald-300">Home</a>
                    <a href="{{ route('browse') }}" class="transition hover:text-emerald-500 dark:hover:text-emerald-300">Browse</a>
                    <a href="{{ route('pricing') }}" class="transition hover:text-emerald-500 dark:hover:text-emerald-300">Pricing</a>
                    @auth
                        <a href="{{ route('account') }}" class="transition hover:text-emerald-500 dark:hover:text-emerald-300">Account</a>
                    @endauth
                    <a href="{{ route('ui.components') }}" class="transition hover:text-emerald-500 dark:hover:text-emerald-300">UI Guide</a>
                </nav>

                <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.28em] text-slate-600 dark:text-slate-300">
                    <x-flux.theme-toggle icon-only class="sm:hidden" />
                    <x-flux.theme-toggle class="hidden sm:inline-flex" />

                    @auth
                        <span class="hidden rounded-full border border-white/20 bg-white/40 px-3 py-1 text-[0.65rem] font-semibold tracking-[0.4em] text-slate-700 dark:border-white/10 dark:bg-white/10 dark:text-slate-200 md:inline-flex">
                            {{ \Illuminate\Support\Str::limit(auth()->user()->name, 14) }}
                        </span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center rounded-full border border-slate-400/40 bg-white/70 px-4 py-1.5 text-[0.7rem] uppercase tracking-[0.38em] text-slate-700 transition hover:border-emerald-400/70 hover:text-emerald-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400/70 dark:border-white/15 dark:bg-slate-900/70 dark:text-slate-200 dark:hover:text-emerald-200">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center rounded-full border border-slate-400/40 bg-white/70 px-4 py-1.5 text-[0.7rem] uppercase tracking-[0.38em] text-slate-700 transition hover:border-emerald-400/70 hover:text-emerald-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400/70 dark:border-white/15 dark:bg-slate-900/70 dark:text-slate-200 dark:hover:text-emerald-200">Sign in</a>
                        <a href="{{ route('register') }}" class="hidden items-center rounded-full bg-gradient-to-r from-emerald-400 via-cyan-400 to-blue-500 px-5 py-1.5 text-[0.7rem] uppercase tracking-[0.4em] text-slate-900 shadow-[var(--shadow-glow)] transition hover:brightness-110 md:inline-flex">Join now</a>
                    @endauth
                </div>
            </div>
        </header>

        <main class="flex-1 py-12 sm:py-16">
            <div class="flux-container space-y-12">
                @isset($header)
                    <div class="flux-spotlight mx-auto max-w-3xl text-center">
                        <p class="text-xs font-semibold uppercase tracking-[0.45em] text-slate-500 dark:text-slate-400">{{ $header }}</p>
                        @isset($subheader)
                            <p class="mt-4 text-base text-slate-600 dark:text-slate-300">{{ $subheader }}</p>
                        @endisset
                    </div>
                @endisset

                {{ $slot ?? '' }}

                @yield('content')
            </div>
        </main>

        <footer class="border-t border-white/20 bg-[color:var(--surface-layer)]/80 py-10 backdrop-blur dark:border-white/10">
            <div class="flux-container flex flex-col gap-4 text-xs font-semibold uppercase tracking-[0.28em] text-slate-500 dark:text-slate-300 sm:flex-row sm:items-center sm:justify-between">
                <p>&copy; {{ now()->year }} OMDb Stream. Crafted with Flux UI.</p>
                <div class="flex items-center gap-4">
                    <a href="#" class="transition hover:text-emerald-500 dark:hover:text-emerald-300">Terms</a>
                    <a href="#" class="transition hover:text-emerald-500 dark:hover:text-emerald-300">Privacy</a>
                    <a href="#" class="transition hover:text-emerald-500 dark:hover:text-emerald-300">Support</a>
                </div>
            </div>
        </footer>
    </div>

    @stack('scripts')

    @livewireScripts
</body>
</html>
