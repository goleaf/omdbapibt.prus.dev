@props(['layout' => 'horizontal'])

@php
$containerClass = $layout === 'horizontal' 
    ? 'flex items-center gap-6' 
    : 'flex flex-col gap-4';

$linkClass = $layout === 'horizontal'
    ? 'text-sm font-medium text-[color:var(--flux-text)] transition hover:text-emerald-400'
    : 'text-base font-semibold text-[color:var(--flux-text)] transition hover:text-emerald-400';
@endphp

<nav {{ $attributes->merge(['class' => $containerClass]) }}>
    <a href="{{ localized_route('home') }}" class="{{ $linkClass }}">
        {{ __('ui.nav.links.home') }}
    </a>
    <a href="{{ localized_route('browse') }}" class="{{ $linkClass }}">
        {{ __('ui.nav.links.browse') }}
    </a>
    <a href="{{ localized_route('pricing') }}" class="{{ $linkClass }}">
        {{ __('ui.nav.links.pricing') }}
    </a>
    <a href="{{ localized_route('ui.components') }}" class="{{ $linkClass }}">
        {{ __('ui.nav.links.components') }}
    </a>
    @auth
        @if (auth()->user()?->isAdmin())
            <a href="{{ localized_route('admin.panel') }}" class="{{ $linkClass }}">
                {{ __('ui.nav.links.admin') }}
            </a>
        @endif
    @endauth
</nav>
