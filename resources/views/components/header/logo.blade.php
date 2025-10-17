<a href="{{ localized_route('home') }}" class="group flex items-center gap-2.5 text-lg font-bold tracking-tight transition-all duration-300 hover:scale-105">
    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-emerald-400 to-emerald-600 text-white shadow-lg shadow-emerald-500/30 transition-all duration-300 group-hover:rotate-12 group-hover:shadow-xl group-hover:shadow-emerald-500/40">
        <svg aria-hidden="true" focusable="false" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <circle cx="12" cy="12" r="9"/>
            <path d="M12 3v18"/>
            <path d="M3 12h18"/>
        </svg>
    </span>
    <span class="bg-gradient-to-r from-white to-slate-300 bg-clip-text text-transparent">
        {{ __('ui.nav.brand.primary') }}<span class="bg-gradient-to-r from-emerald-400 to-emerald-300 bg-clip-text">{{ __('ui.nav.brand.secondary') }}</span>
    </span>
</a>
