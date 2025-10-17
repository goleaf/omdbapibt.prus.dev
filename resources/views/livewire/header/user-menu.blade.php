<div class="relative" x-data="{ open: @entangle('isOpen') }" x-on:click.away="open = false" x-on:keydown.window.escape="open = false">
    <button
        type="button"
        wire:click="toggle"
        class="group flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-emerald-400 to-emerald-600 font-bold text-white shadow-lg shadow-emerald-500/30 transition-all duration-300 hover:scale-105 hover:shadow-xl hover:shadow-emerald-500/40 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:ring-offset-2"
        aria-label="{{ __('ui.nav.user_menu.dropdown_label') }}"
        aria-expanded="{{ $isOpen ? 'true' : 'false' }}"
    >
        <span class="text-sm uppercase">
            {{ substr($user?->name ?? 'U', 0, 1) }}
        </span>
    </button>

    <!-- Dropdown menu -->
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
        class="user-menu-dropdown absolute right-0 top-full mt-2 w-48 origin-top-right overflow-hidden rounded-xl border border-[color:var(--flux-border-soft)] bg-[color:var(--flux-surface-card)] py-1 shadow-xl backdrop-blur-sm"
        x-cloak
    >
        <div class="border-b border-[color:var(--flux-border-soft)] px-4 py-3">
            <p class="truncate text-sm font-semibold text-[color:var(--flux-text)]">{{ $user?->name ?? 'User' }}</p>
            <p class="truncate text-xs text-[color:var(--flux-text-muted)]">{{ $user?->email ?? '' }}</p>
        </div>

        <div class="py-1">
            <a
                href="{{ localized_route('account') }}"
                class="block px-4 py-2 text-sm text-[color:var(--flux-text)] transition hover:bg-emerald-500/10 hover:text-emerald-400"
                wire:click="close"
            >
                {{ __('ui.nav.user_menu.account') }}
            </a>

            @if ($user?->isAdmin())
                <a
                    href="{{ localized_route('admin.panel') }}"
                    class="block px-4 py-2 text-sm text-[color:var(--flux-text)] transition hover:bg-emerald-500/10 hover:text-emerald-400"
                    wire:click="close"
                >
                    {{ __('ui.nav.links.admin') }}
                </a>
            @endif
        </div>

        <div class="border-t border-[color:var(--flux-border-soft)] py-1">
            <form method="POST" action="{{ localized_route('logout') }}">
                @csrf
                <button
                    type="submit"
                    class="block w-full px-4 py-2 text-left text-sm text-red-400 transition hover:bg-red-500/10"
                >
                    {{ __('ui.nav.auth.logout') }}
                </button>
            </form>
        </div>
    </div>
</div>
