<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name', 'OMDb API BT') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full bg-slate-950 text-slate-100">
    <div class="min-h-full">
        <header class="bg-slate-900/80 backdrop-blur border-b border-slate-800">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-5">
                <a href="{{ route('home') }}" class="text-lg font-semibold tracking-wide">
                    <span class="text-slate-100">OMDb</span>
                    <span class="text-emerald-400">Stream</span>
                </a>
                <nav class="hidden gap-8 text-sm font-medium md:flex">
                    <a href="{{ route('home') }}" class="transition hover:text-emerald-300">Home</a>
                    <a href="{{ route('browse') }}" class="transition hover:text-emerald-300">Browse</a>
                    <a href="{{ route('pricing') }}" class="transition hover:text-emerald-300">Pricing</a>
                    @auth
                        <a href="{{ route('account') }}" class="transition hover:text-emerald-300">Account</a>
                    @endauth
                </nav>
                <div class="flex items-center gap-3 text-sm">
                    @auth
                        <span class="hidden text-slate-300 md:inline">{{ auth()->user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="rounded-full border border-slate-700 px-4 py-1.5 text-slate-200 transition hover:border-emerald-400 hover:text-emerald-200">Logout</button>
                        </form>
                    @else
                        @if (Route::has('login'))
                            <a href="{{ route('login') }}" class="rounded-full border border-slate-700 px-4 py-1.5 transition hover:border-emerald-400 hover:text-emerald-200">Sign in</a>
                        @endif
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="hidden rounded-full bg-emerald-500 px-4 py-1.5 font-semibold text-emerald-950 transition hover:bg-emerald-400 md:inline">Join now</a>
                        @endif
                    @endauth
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            @isset($header)
                <div class="mb-8 text-center">
                    <h1 class="text-3xl font-bold tracking-tight text-slate-50 sm:text-4xl">{{ $header }}</h1>
                    @isset($subheader)
                        <p class="mt-2 text-base text-slate-400">{{ $subheader }}</p>
                    @endisset
                </div>
            @endisset

            {{ $slot ?? '' }}

            @yield('content')
        </main>

        <footer class="border-t border-slate-800 bg-slate-900/70 py-8">
            <div class="mx-auto flex max-w-7xl flex-col gap-4 px-6 text-sm text-slate-400 sm:flex-row sm:items-center sm:justify-between">
                <p>&copy; {{ now()->year }} OMDb Stream. All rights reserved.</p>
                <div class="flex items-center gap-4">
                    <a href="#" class="transition hover:text-emerald-300">Terms</a>
                    <a href="#" class="transition hover:text-emerald-300">Privacy</a>
                    <a href="#" class="transition hover:text-emerald-300">Support</a>
                </div>
            </div>
        </footer>
    </div>

    @livewireScripts
</body>
</html>
