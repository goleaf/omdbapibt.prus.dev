<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full antialiased" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name', 'OMDb Stream') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-transparent">
    <div class="relative isolate flex min-h-screen flex-col">
        <div class="pointer-events-none absolute inset-x-0 top-[-200px] z-[-1] blur-3xl">
            <div class="mx-auto h-[420px] w-[820px] rounded-full bg-emerald-500/20 motion-soft-glow"></div>
        </div>

        <header class="surface-shell border-b">
            <div class="mx-auto flex w-full max-w-7xl items-center justify-between px-6 py-5">
                <a href="{{ route('home') }}" class="flex items-center gap-2 text-lg font-semibold tracking-wide">
                    <span class="text-emerald-400">◎</span>
                    <span>{{ __('ui.nav.brand.primary') }}<span class="text-emerald-400">{{ __('ui.nav.brand.secondary') }}</span></span>
                </a>

                <nav class="hidden items-center gap-8 text-sm font-medium md:flex">
                    <a href="{{ route('home') }}" class="flux-text-muted transition hover:text-emerald-300">{{ __('ui.nav.links.home') }}</a>
                    <a href="{{ route('browse') }}" class="flux-text-muted transition hover:text-emerald-300">{{ __('ui.nav.links.browse') }}</a>
                    <a href="{{ route('pricing') }}" class="flux-text-muted transition hover:text-emerald-300">{{ __('ui.nav.links.pricing') }}</a>
                    <a href="{{ route('ui.components') }}" class="flux-text-muted transition hover:text-emerald-300">{{ __('ui.nav.links.components') }}</a>
                    @auth
                        <a href="{{ route('account') }}" class="flux-text-muted transition hover:text-emerald-300">{{ __('ui.nav.links.account') }}</a>
                        @if (auth()->user()?->isAdmin())
                            <a href="{{ route('admin.analytics') }}" class="flux-text-muted transition hover:text-emerald-300">{{ __('ui.nav.links.admin') }}</a>
                        @endif
                    @endauth
                </nav>

                <div class="flex items-center gap-3 text-sm">
                    <x-theme-toggle class="hidden md:inline-flex" />

                    @auth
                        <span class="hidden flux-text-muted md:inline">{{ auth()->user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="rounded-full border border-[color:var(--flux-border-soft)] px-4 py-1.5 transition hover:border-emerald-400 hover:text-emerald-200">{{ __('ui.nav.auth.logout') }}</button>
                        </form>
                    @else
                        @if (Route::has('login'))
                            <a href="{{ route('login') }}" class="rounded-full border border-[color:var(--flux-border-soft)] px-4 py-1.5 transition hover:border-emerald-400 hover:text-emerald-200">{{ __('ui.nav.auth.login') }}</a>
                        @endif
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="hidden rounded-full bg-emerald-500 px-4 py-1.5 font-semibold text-emerald-950 transition hover:bg-emerald-400 md:inline">{{ __('ui.nav.auth.register') }}</a>
                        @endif
                    @endauth
                </div>
            </div>
        </header>

        <main class="mx-auto w-full max-w-7xl flex-1 px-4 py-10 sm:px-6 lg:px-8">
            @isset($header)
                <div class="mb-8 text-center">
                    <h1 class="text-3xl font-bold tracking-tight sm:text-4xl">{{ $header }}</h1>
                    @isset($subheader)
                        <p class="mt-2 text-base flux-text-muted">{{ $subheader }}</p>
                    @endisset
                </div>
            @endisset

            {{ $slot ?? '' }}

            @yield('content')
        </main>

        <footer class="surface-shell border-t py-8">
            <div class="mx-auto flex w-full max-w-7xl flex-col gap-4 px-6 text-sm flux-text-muted sm:flex-row sm:items-center sm:justify-between">
                <p>{{ __('ui.nav.footer.copyright', ['year' => now()->year]) }}</p>
                <div class="flex items-center gap-4">
                    <a href="#" class="transition hover:text-emerald-300">{{ __('ui.nav.footer.terms') }}</a>
                    <a href="#" class="transition hover:text-emerald-300">{{ __('ui.nav.footer.privacy') }}</a>
                    <a href="#" class="transition hover:text-emerald-300">{{ __('ui.nav.footer.support') }}</a>
                </div>
            </div>
        </footer>
    </div>

    @livewireScripts
    @stack('scripts')
</body>
</html>
