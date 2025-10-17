<div>
    <!-- Mobile panel -->
    <div
        x-data="{ open: @entangle('isOpen') }"
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="translate-x-full opacity-0"
        x-transition:enter-end="translate-x-0 opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="translate-x-0 opacity-100"
        x-transition:leave-end="translate-x-full opacity-0"
        class="fixed inset-y-0 right-0 z-50 flex w-full max-w-xs flex-col gap-6 overflow-y-auto bg-[color:var(--flux-surface-1)] px-6 py-6 shadow-xl md:hidden"
        role="dialog"
        aria-modal="true"
        x-cloak
    >
        <!-- Header -->
        <div class="flex items-center justify-between">
            <p class="text-sm font-semibold uppercase tracking-wide text-emerald-200">
                {{ __('ui.nav.menu.label') }}
            </p>

            <button
                type="button"
                wire:click="close"
                class="group inline-flex h-10 w-10 items-center justify-center rounded-xl border border-[color:var(--flux-border-soft)] bg-[color:var(--flux-surface-card)] text-[color:var(--flux-text-muted)] backdrop-blur-sm transition-all duration-300 hover:rotate-90 hover:scale-105 hover:border-red-400 hover:bg-red-500/10 hover:text-red-400 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-2"
                aria-label="{{ __('ui.nav.menu.close') }}"
            >
                <svg class="h-5 w-5 transition-transform duration-300" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.75">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.25l9.5 9.5M14.75 5.25l-9.5 9.5" />
                </svg>
            </button>
        </div>

        <!-- Navigation links -->
        <x-navigation-links layout="vertical" class="flex flex-col gap-4" wire:click="close" />

        <!-- Auth / User section -->
        <div class="flex flex-col gap-4 border-t border-[color:var(--flux-border-soft)] pt-4">
            @if ($user)
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-emerald-400 to-emerald-600 font-bold text-white shadow-lg">
                        <span class="text-sm uppercase">{{ substr($user->name ?? 'U', 0, 1) }}</span>
                    </div>
                    <div class="flex-1 overflow-hidden">
                        <p class="truncate text-sm font-semibold text-[color:var(--flux-text)]">{{ $user->name }}</p>
                        <p class="truncate text-xs text-[color:var(--flux-text-muted)]">{{ $user->email }}</p>
                    </div>
                </div>

                <a
                    href="{{ localized_route('account') }}"
                    wire:click="close"
                    class="rounded-xl border border-[color:var(--flux-border-soft)] bg-[color:var(--flux-surface-card)] px-4 py-2 text-center text-sm font-medium backdrop-blur-sm transition hover:border-emerald-400 hover:bg-emerald-500/10 hover:text-emerald-400"
                >
                    {{ __('ui.nav.user_menu.account') }}
                </a>

                <form method="POST" action="{{ localized_route('logout') }}">
                    @csrf
                    <button
                        type="submit"
                        class="w-full rounded-xl border border-[color:var(--flux-border-soft)] bg-[color:var(--flux-surface-card)] px-4 py-2 text-sm font-medium backdrop-blur-sm transition hover:border-red-400 hover:bg-red-500/10 hover:text-red-400"
                    >
                        {{ __('ui.nav.auth.logout') }}
                    </button>
                </form>
            @else
                <div class="flex flex-col gap-3">
                    @if ($hasLogin)
                        <a
                            href="{{ localized_route('login') }}"
                            wire:click="close"
                            class="rounded-xl border border-[color:var(--flux-border-soft)] bg-[color:var(--flux-surface-card)] px-4 py-2 text-center font-medium backdrop-blur-sm transition hover:border-emerald-400 hover:bg-emerald-500/10 hover:text-emerald-400"
                        >
                            {{ __('ui.nav.auth.login') }}
                        </a>
                    @endif

                    @if ($hasRegister)
                        <a
                            href="{{ localized_route('register') }}"
                            wire:click="close"
                            class="rounded-xl bg-gradient-to-r from-emerald-500 to-emerald-600 px-5 py-2 text-center font-bold text-white shadow-lg shadow-emerald-500/30 transition hover:scale-105 hover:from-emerald-400 hover:to-emerald-500 hover:shadow-xl hover:shadow-emerald-500/40"
                        >
                            {{ __('ui.nav.auth.register') }}
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Backdrop -->
    <div
        x-data="{ open: @entangle('isOpen') }"
        x-show="open"
        x-on:click="open = false"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-40 bg-slate-950/60 md:hidden"
        x-cloak
    ></div>
</div>
