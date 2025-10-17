<button
    type="button"
    x-on:click="$dispatch('toggleMobileMenu')"
    class="group inline-flex h-10 w-10 items-center justify-center rounded-xl border border-[color:var(--flux-border-soft)] bg-[color:var(--flux-surface-card)] text-[color:var(--flux-text-muted)] backdrop-blur-sm transition-all duration-300 hover:scale-105 hover:border-emerald-400 hover:bg-emerald-500/10 hover:text-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:ring-offset-2 md:hidden"
    aria-label="{{ __('ui.nav.menu.open') }}"
>
    <svg class="h-5 w-5 transition-transform duration-300 group-hover:scale-110" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3.25 6.25h13.5M3.25 10h13.5M3.25 13.75h13.5" />
    </svg>
</button>

