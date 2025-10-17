@props([
    'user' => auth()->user(),
])

@php
    $hasLogin = Route::has('login');
    $hasRegister = Route::has('register');
    $userName = $user?->name;
@endphp

<header
    data-layout-navigation
    data-mobile-nav
    {{ $attributes->merge(['class' => 'surface-shell border-b sticky top-0 z-50']) }}
>
    <div class="mx-auto flex w-full max-w-screen-2xl items-center justify-between gap-4 px-6 py-4 2xl:px-12">
        <div class="flex items-center gap-4">
            <button
                type="button"
                class="group inline-flex h-10 w-10 items-center justify-center rounded-xl border border-[color:var(--flux-border-soft)] bg-[color:var(--flux-surface-card)] text-[color:var(--flux-text-muted)] backdrop-blur-sm transition-all duration-300 hover:border-emerald-400 hover:bg-emerald-500/10 hover:text-emerald-400 hover:scale-105 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400 focus-visible:ring-offset-2 md:hidden"
                data-mobile-nav-open
                aria-controls="mobile-navigation"
                aria-expanded="false"
                aria-label="{{ __('ui.nav.menu.open') }}"
            >
                <svg class="h-5 w-5 transition-transform duration-300 group-hover:scale-110" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.25 6.25h13.5M3.25 10h13.5M3.25 13.75h13.5" />
                </svg>
            </button>

            <a href="{{ localized_route('home') }}" class="group flex items-center gap-2.5 text-lg font-bold tracking-tight transition-all duration-300 hover:scale-105">
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-emerald-400 to-emerald-600 text-white shadow-lg shadow-emerald-500/30 transition-all duration-300 group-hover:shadow-xl group-hover:shadow-emerald-500/40 group-hover:rotate-12">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <circle cx="12" cy="12" r="9"/>
                        <path d="M12 3v18"/>
                        <path d="M3 12h18"/>
                    </svg>
                </span>
                <span class="bg-gradient-to-r from-white to-slate-300 bg-clip-text text-transparent">
                    {{ __('ui.nav.brand.primary') }}<span class="from-emerald-400 to-emerald-300 bg-gradient-to-r bg-clip-text">{{ __('ui.nav.brand.secondary') }}</span>
                </span>
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
                    class="group inline-flex h-10 w-10 items-center justify-center rounded-xl border border-[color:var(--flux-border-soft)] bg-[color:var(--flux-surface-card)] text-[color:var(--flux-text-muted)] backdrop-blur-sm transition-all duration-300 hover:border-red-400 hover:bg-red-500/10 hover:text-red-400 hover:scale-105 hover:rotate-90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-400 focus-visible:ring-offset-2"
                    data-mobile-nav-close
                    aria-label="{{ __('ui.nav.menu.close') }}"
                >
                    <svg class="h-5 w-5 transition-transform duration-300" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.25l9.5 9.5M14.75 5.25l-9.5 9.5" />
                    </svg>
                </button>
            </div>

            <x-navigation-links layout="vertical" class="md:flex md:items-center md:gap-8 md:text-sm" />

            <div class="flex flex-col gap-4 border-t border-[color:var(--flux-border-soft)] pt-4 text-sm md:flex-row md:items-center md:gap-3 md:border-0 md:pt-0">
                <x-theme-toggle class="inline-flex" />

                @if ($user)
                    <span class="rounded-full bg-emerald-500/10 px-3 py-1 text-sm font-semibold text-emerald-400 backdrop-blur-sm">{{ $userName }}</span>
                    <form method="POST" action="{{ localized_route('logout') }}" class="md:flex md:items-center">
                        @csrf
                        <button type="submit" class="rounded-xl border border-[color:var(--flux-border-soft)] bg-[color:var(--flux-surface-card)] px-4 py-2 font-medium backdrop-blur-sm transition-all duration-300 hover:border-emerald-400 hover:bg-emerald-500/10 hover:text-emerald-400 hover:scale-105 hover:shadow-lg focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400 focus-visible:ring-offset-2">{{ __('ui.nav.auth.logout') }}</button>
                    </form>
                @else
                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:gap-3">
                        @if ($hasLogin)
                            <a href="{{ localized_route('login') }}" class="rounded-xl border border-[color:var(--flux-border-soft)] bg-[color:var(--flux-surface-card)] px-4 py-2 text-center font-medium backdrop-blur-sm transition-all duration-300 hover:border-emerald-400 hover:bg-emerald-500/10 hover:text-emerald-400 hover:scale-105 hover:shadow-lg focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400 focus-visible:ring-offset-2">{{ __('ui.nav.auth.login') }}</a>
                        @endif
                        @if ($hasRegister)
                            <a href="{{ localized_route('register') }}" class="group rounded-xl bg-gradient-to-r from-emerald-500 to-emerald-600 px-5 py-2 text-center font-bold text-white shadow-lg shadow-emerald-500/30 transition-all duration-300 hover:scale-105 hover:shadow-xl hover:shadow-emerald-500/40 hover:from-emerald-400 hover:to-emerald-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400 focus-visible:ring-offset-2">{{ __('ui.nav.auth.register') }}</a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div
        data-mobile-nav-backdrop
        class="fixed inset-0 z-40 bg-slate-950/60 opacity-0 transition duration-200 ease-out pointer-events-none md:hidden"
    ></div>
</header>
