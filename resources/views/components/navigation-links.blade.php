<nav {{ $attributes->class($containerClass) }}>
    <a href="{{ localized_route('home') }}" class="{{ $linkClass }}">{{ __('ui.nav.links.home') }}</a>
    <a href="{{ localized_route('browse') }}" class="{{ $linkClass }}">{{ __('ui.nav.links.browse') }}</a>
    <a href="{{ localized_route('pricing') }}" class="{{ $linkClass }}">{{ __('ui.nav.links.pricing') }}</a>
    <a href="{{ localized_route('about') }}" class="{{ $linkClass }}">{{ __('ui.nav.links.about') }}</a>
    <a href="{{ localized_route('support') }}" class="{{ $linkClass }}">{{ __('ui.nav.links.support') }}</a>
    <a href="{{ localized_route('ui.components') }}" class="{{ $linkClass }}">{{ __('ui.nav.links.components') }}</a>
    @auth
        <a href="{{ localized_route('account') }}" class="{{ $linkClass }}">{{ __('ui.nav.links.account') }}</a>
        @if (auth()->user()?->isAdmin())
            <a href="{{ localized_route('admin.panel') }}" class="{{ $linkClass }}">{{ __('ui.nav.links.admin') }}</a>
        @endif
    @endauth
</nav>
