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
        <!-- Enhanced Background Effects -->
        <div class="pointer-events-none fixed inset-0 z-[-1] overflow-hidden">
            <div class="absolute left-1/4 top-[-10%] h-[500px] w-[500px] rounded-full bg-gradient-to-br from-emerald-500/20 to-emerald-600/10 blur-3xl motion-soft-glow"></div>
            <div class="absolute right-1/4 top-[10%] h-[400px] w-[400px] rounded-full bg-gradient-to-br from-blue-500/15 to-blue-600/10 blur-3xl motion-float"></div>
            <div class="absolute left-1/3 bottom-[20%] h-[350px] w-[350px] rounded-full bg-gradient-to-br from-purple-500/10 to-purple-600/5 blur-3xl" style="animation: soft-glow 18s ease-in-out infinite 2s;"></div>
        </div>
        
        <!-- Grid Pattern Overlay -->
        <div class="pointer-events-none fixed inset-0 z-[-1] opacity-[0.02]" style="background-image: radial-gradient(circle at center, currentColor 1px, transparent 1px); background-size: 40px 40px;"></div>

        @php
            $footerLinks = [
                [
                    'label' => __('ui.nav.footer.terms'),
                    'href' => localized_route('terms'),
                ],
                [
                    'label' => __('ui.nav.footer.privacy'),
                    'href' => localized_route('privacy'),
                ],
                [
                    'label' => __('ui.nav.footer.support'),
                    'href' => localized_route('support'),
                ],
            ];
        @endphp

        <x-layout.navigation />

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

        <main class="relative mx-auto w-full max-w-screen-2xl flex-1 px-4 py-12 sm:px-6 lg:px-8 2xl:px-12">
            @isset($header)
                <div class="mb-12 text-center">
                    <h1 class="bg-gradient-to-r from-white via-slate-200 to-slate-300 bg-clip-text text-4xl font-bold tracking-tight text-transparent sm:text-5xl lg:text-6xl">
                        {{ $header }}
                    </h1>
                    @isset($subheader)
                        <p class="mx-auto mt-4 max-w-2xl text-lg flux-text-muted">{{ $subheader }}</p>
                    @endisset
                </div>
            @endisset

            {{ $slot ?? '' }}

            @yield('content')
        </main>

        <x-layout.footer :links="$footerLinks" />
    </div>

    @livewireScripts
    @stack('scripts')
</body>
</html>
