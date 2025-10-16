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
    {{ $attributes->merge(['class' => 'surface-shell border-b']) }}
>
    <div class="mx-auto flex w-full max-w-screen-2xl items-center justify-between gap-4 px-6 py-5 2xl:px-12">
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

                @if ($user)
                    <span class="flux-text-muted">{{ $userName }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="md:flex md:items-center">
                        @csrf
                        <button type="submit" class="rounded-full border border-[color:var(--flux-border-soft)] px-4 py-1.5 transition hover:border-emerald-400 hover:text-emerald-200">{{ __('ui.nav.auth.logout') }}</button>
                    </form>
                @else
                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:gap-3">
                        @if ($hasLogin)
                            <a href="{{ route('login') }}" class="rounded-full border border-[color:var(--flux-border-soft)] px-4 py-1.5 text-center transition hover:border-emerald-400 hover:text-emerald-200">{{ __('ui.nav.auth.login') }}</a>
                        @endif
                        @if ($hasRegister)
                            <a href="{{ route('register') }}" class="rounded-full bg-emerald-500 px-4 py-1.5 text-center font-semibold text-emerald-950 transition hover:bg-emerald-400">{{ __('ui.nav.auth.register') }}</a>
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
