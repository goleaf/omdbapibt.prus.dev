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

        <header class="surface-shell border-b" data-mobile-nav>
            <div class="mx-auto flex w-full max-w-7xl items-center justify-between gap-4 px-6 py-5">
                <div class="flex items-center gap-3">
                    <button
                        type="button"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-[color:var(--flux-border-soft)] text-[color:var(--flux-text-muted)] transition hover:border-emerald-400 hover:text-emerald-200 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-400 md:hidden"
                        data-mobile-nav-open
                        aria-controls="mobile-navigation"
                        aria-expanded="false"
                        aria-label="{{ __('ui.nav.menu.open') }}"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.25 6.25h13.5M3.25 10h13.5M3.25 13.75h13.5" />
                        </svg>
                    </button>

                    <a href="{{ route('home') }}" class="flex items-center gap-2 text-lg font-semibold tracking-wide">
                        <span class="text-emerald-400">◎</span>
                        <span>{{ __('ui.nav.brand.primary') }}<span class="text-emerald-400">{{ __('ui.nav.brand.secondary') }}</span></span>
                    </a>
                </div>

                <div
                    id="mobile-navigation"
                    data-mobile-nav-panel
                    class="fixed inset-y-0 right-0 z-50 flex w-full max-w-xs translate-x-full flex-col gap-6 overflow-y-auto bg-[color:var(--flux-surface-1)] px-6 py-6 opacity-0 shadow-xl transition duration-200 ease-out pointer-events-none md:relative md:ml-auto md:flex md:max-w-none md:translate-x-0 md:flex-row md:items-center md:gap-6 md:bg-transparent md:px-0 md:py-0 md:opacity-100 md:pointer-events-auto md:shadow-none"
                    role="dialog"
                    aria-modal="false"
                    aria-labelledby="mobile-navigation-title"
                    aria-hidden="true"
                    tabindex="-1"
                >
                    <div class="flex items-center justify-between md:hidden">
                        <p id="mobile-navigation-title" class="text-sm font-semibold uppercase tracking-wide text-emerald-200">{{ __('ui.nav.menu.label') }}</p>

                        <button
                            type="button"
                            class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-[color:var(--flux-border-soft)] text-[color:var(--flux-text-muted)] transition hover:border-emerald-400 hover:text-emerald-200 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-400"
                            data-mobile-nav-close
                            aria-label="{{ __('ui.nav.menu.close') }}"
                        >
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.25l9.5 9.5M14.75 5.25l-9.5 9.5" />
                            </svg>
                        </button>
                    </div>

                    <x-navigation-links layout="vertical" class="md:flex md:items-center md:gap-8 md:text-sm" />

                    <div class="flex flex-col gap-4 border-t border-[color:var(--flux-border-soft)] pt-4 text-sm md:flex-row md:items-center md:gap-3 md:border-0 md:pt-0">
                        <x-theme-toggle class="inline-flex" />

                        @auth
                            <span class="flux-text-muted">{{ auth()->user()->name }}</span>
                            <form method="POST" action="{{ route('logout') }}" class="md:flex md:items-center">
                                @csrf
                                <button type="submit" class="rounded-full border border-[color:var(--flux-border-soft)] px-4 py-1.5 transition hover:border-emerald-400 hover:text-emerald-200">{{ __('ui.nav.auth.logout') }}</button>
                            </form>
                        @else
                            <div class="flex flex-col gap-3 md:flex-row md:items-center md:gap-3">
                                @if (Route::has('login'))
                                    <a href="{{ route('login') }}" class="rounded-full border border-[color:var(--flux-border-soft)] px-4 py-1.5 text-center transition hover:border-emerald-400 hover:text-emerald-200">{{ __('ui.nav.auth.login') }}</a>
                                @endif
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="rounded-full bg-emerald-500 px-4 py-1.5 text-center font-semibold text-emerald-950 transition hover:bg-emerald-400">{{ __('ui.nav.auth.register') }}</a>
                                @endif
                            </div>
                        @endauth
                    </div>
                </div>
            </div>

            <div
                data-mobile-nav-backdrop
                class="fixed inset-0 z-40 bg-slate-950/60 opacity-0 transition duration-200 ease-out pointer-events-none md:hidden"
            ></div>
        </header>

        @isset($impersonationBannerContext)
            <div class="border-b border-amber-400/40 bg-amber-500/10 py-3">
                <div class="mx-auto flex w-full max-w-7xl flex-col gap-3 px-6 text-sm text-amber-100 sm:flex-row sm:items-center sm:justify-between">
                    <div class="space-y-1">
                        <p class="font-semibold tracking-wide uppercase text-amber-200">Impersonation Active</p>
                        <p>
                            <span class="font-medium">{{ $impersonationBannerContext['impersonator']->name }}</span>
                            <span class="text-amber-200">({{ $impersonationBannerContext['impersonator']->email }})</span>
                            is currently viewing the site as
                            <span class="font-medium">{{ $impersonationBannerContext['actingAs']->name }}</span>
                            <span class="text-amber-200">({{ $impersonationBannerContext['actingAs']->email }})</span>.
                        </p>
                    </div>
                    <form method="POST" action="{{ route('impersonation.stop') }}" class="sm:flex-shrink-0">
                        @csrf
                        <button
                            type="submit"
                            class="inline-flex items-center rounded-full border border-amber-300 px-4 py-1.5 text-xs font-semibold uppercase tracking-wide text-amber-900 transition hover:bg-amber-400 hover:text-amber-950"
                        >
                            Stop impersonating
                        </button>
                    </form>
                </div>
            </div>
        @endisset

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
