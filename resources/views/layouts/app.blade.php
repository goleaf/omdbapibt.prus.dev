<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full antialiased" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name', 'OMDb Stream') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
@inject('impersonationManager', \App\Support\ImpersonationManager::class)

<body class="min-h-screen bg-transparent text-slate-100">
    <div class="relative isolate flex min-h-screen flex-col">
        <div class="pointer-events-none absolute inset-x-0 top-[-200px] z-[-1] blur-3xl">
            <div class="mx-auto h-[420px] w-[820px] rounded-full bg-emerald-500/20 motion-soft-glow"></div>
        </div>

        <x-layout.navigation :user="auth()->user()" />

        @isset($impersonationBannerContext)
            <div class="border-b border-amber-400/40 bg-amber-500/10 py-3">
                <div class="mx-auto flex w-full max-w-screen-2xl flex-col gap-3 px-6 text-sm text-amber-100 sm:flex-row sm:items-center sm:justify-between 2xl:px-12">
                    <div class="space-y-1">
                        <p class="font-semibold tracking-wide uppercase text-amber-200">Impersonation Active</p>
                        <p>
                            <span class="font-medium">{{ $impersonationBannerContext['impersonator']->name }}</span>
                            <span class="text-amber-200">({{ $impersonationBannerContext['impersonator']->email }})</span>
                            is currently viewing the site as
                            <span class="font-medium">{{ $impersonationBannerContext['actingAs']->name }}</span>
                            <span class="text-amber-200">({{ $impersonationBannerContext['actingAs']->email }})</span>.
                        </p>
                    </div>
                    <form method="POST" action="{{ route('impersonation.stop') }}" class="sm:flex-shrink-0">
                        @csrf
                        <button
                            type="submit"
                            class="inline-flex items-center rounded-full border border-amber-300 px-4 py-1.5 text-xs font-semibold uppercase tracking-wide text-amber-900 transition hover:bg-amber-400 hover:text-amber-950"
                        >
                            Stop impersonating
                        </button>
                    </form>
                </div>
            </div>
        @endisset

        <main class="mx-auto w-full max-w-7xl flex-1 px-4 py-10 sm:px-6 lg:px-8">
            @if ($impersonationManager->isImpersonating() && auth()->check())
                <div class="mb-6 flex flex-col gap-3 rounded-3xl border border-amber-400/60 bg-amber-500/10 px-4 py-4 text-amber-50 shadow-lg shadow-amber-500/10 md:flex-row md:items-center md:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-200">
                            {{ __('ui.impersonation.banner_title', ['name' => auth()->user()->name]) }}
                        </p>
                        <p class="mt-2 text-sm text-amber-100/80">
                            {{ __('ui.impersonation.banner_help') }}
                        </p>
                    </div>
                    <form method="POST" action="{{ route('impersonation.stop') }}" class="flex-shrink-0">
                        @csrf
                        @method('DELETE')

                        <button
                            type="submit"
                            class="rounded-full border border-amber-400/70 px-4 py-2 text-xs font-semibold uppercase tracking-[0.25em] text-amber-50 transition hover:bg-amber-500/20"
                        >
                            {{ __('ui.impersonation.stop') }}
                        </button>
                    </form>
                </div>
            @endif

            @isset($header)
                <div class="mb-8 text-center">
                    <h1 class="text-3xl font-bold tracking-tight sm:text-4xl">{{ $header }}</h1>
                    @isset($subheader)
                        <p class="mt-2 text-base flux-text-muted">{{ $subheader }}</p>
                    @endisset
                </div>
            @endisset

            {{ $slot ?? '' }}

            @yield('content')
        </main>

        @php
            $configuredFooterLinks = collect(config('site.footer.links', []))
                ->map(function (array $link) {
                    $href = null;

                    if (isset($link['route'])) {
                        $href = route($link['route'], $link['parameters'] ?? []);
                    }

                    if (! $href && isset($link['url'])) {
                        $href = $link['url'];
                    }

                    return [
                        'label' => __($link['label'] ?? ''),
                        'href' => $href,
                        'target' => $link['target'] ?? null,
                        'rel' => $link['rel'] ?? null,
                    ];
                })
                ->filter(fn (array $link) => filled($link['label']) && filled($link['href']))
                ->values()
                ->all();
        @endphp

        <x-layout.footer
            :links="$configuredFooterLinks"
            :copyright="__('ui.nav.footer.copyright', ['year' => now()->year])"
        />
    </div>

    @livewireScripts
    @stack('scripts')
</body>
</html>
