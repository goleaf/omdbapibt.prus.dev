@props(['hasLogin' => true, 'hasRegister' => true])

<div {{ $attributes->merge(['class' => 'flex items-center gap-3']) }}>
    @if ($hasLogin)
        <a
            href="{{ localized_route('login') }}"
            class="rounded-xl border border-[color:var(--flux-border-soft)] bg-[color:var(--flux-surface-card)] px-4 py-2 text-center font-medium backdrop-blur-sm transition-all duration-300 hover:scale-105 hover:border-emerald-400 hover:bg-emerald-500/10 hover:text-emerald-400 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:ring-offset-2"
        >
            {{ __('ui.nav.auth.login') }}
        </a>
    @endif

    @if ($hasRegister)
        <a
            href="{{ localized_route('register') }}"
            class="group rounded-xl bg-gradient-to-r from-emerald-500 to-emerald-600 px-5 py-2 text-center font-bold text-white shadow-lg shadow-emerald-500/30 transition-all duration-300 hover:scale-105 hover:from-emerald-400 hover:to-emerald-500 hover:shadow-xl hover:shadow-emerald-500/40 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:ring-offset-2"
        >
            {{ __('ui.nav.auth.register') }}
        </a>
    @endif
</div>

