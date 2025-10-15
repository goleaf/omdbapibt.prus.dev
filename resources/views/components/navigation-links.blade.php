@props([
    'layout' => 'horizontal',
    'linkClass' => 'flux-text-muted transition hover:text-emerald-300',
])

@php
    $baseClasses = [
        'horizontal' => 'flex items-center gap-8 text-sm font-medium',
        'vertical' => 'flex flex-col gap-6 text-base font-medium',
    ];

    $containerClasses = $baseClasses[$layout] ?? $baseClasses['horizontal'];
@endphp

<nav {{ $attributes->class($containerClasses) }}>
    <a href="{{ route('home') }}" class="{{ $linkClass }}">{{ __('ui.nav.links.home') }}</a>
    <a href="{{ route('browse') }}" class="{{ $linkClass }}">{{ __('ui.nav.links.browse') }}</a>
    <a href="{{ route('pricing') }}" class="{{ $linkClass }}">{{ __('ui.nav.links.pricing') }}</a>
    <a href="{{ route('ui.components') }}" class="{{ $linkClass }}">{{ __('ui.nav.links.components') }}</a>
    @auth
        <a href="{{ route('account') }}" class="{{ $linkClass }}">{{ __('ui.nav.links.account') }}</a>
        @if (auth()->user()?->isAdmin())
            <a href="{{ route('admin.analytics') }}" class="{{ $linkClass }}">{{ __('ui.nav.links.admin') }}</a>
        @endif
    @endauth
</nav>
