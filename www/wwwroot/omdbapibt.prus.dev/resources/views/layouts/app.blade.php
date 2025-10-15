<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', $title ?? config('app.name', 'Laravel'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="antialiased bg-gray-100 text-gray-900">
    <div class="min-h-screen">
        <header class="bg-white shadow">
            <div class="mx-auto max-w-7xl px-6 py-4 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <h1 class="text-2xl font-semibold">
                    @yield('page-title', $pageTitle ?? ($title ?? config('app.name', 'Laravel')))
                </h1>

                <nav class="flex flex-wrap items-center gap-4 text-sm font-medium">
                    <a
                        class="{{ request()->routeIs('dashboard') ? 'text-gray-900 font-semibold' : 'text-blue-600 hover:text-blue-500' }}"
                        href="{{ route('dashboard') }}"
                    >
                        Overview
                    </a>
                    <a
                        class="{{ request()->routeIs('watch-history') ? 'text-gray-900 font-semibold' : 'text-blue-600 hover:text-blue-500' }}"
                        href="{{ route('watch-history') }}"
                    >
                        Watch History
                    </a>
                    <a class="text-blue-600 hover:text-blue-500" href="{{ route('billing.portal') }}">
                        Manage Subscription
                    </a>
                </nav>
            </div>
        </header>

        <main class="mx-auto max-w-7xl px-6 py-10 space-y-6">
            @if (session('error'))
                <div class="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')

            {{ $slot ?? '' }}
        </main>
    </div>

    @livewireScripts
</body>
</html>
