<nav {{ $attributes->class($containerClass) }}>
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
