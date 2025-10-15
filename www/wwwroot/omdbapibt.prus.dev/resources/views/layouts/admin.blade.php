<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }} — Admin Analytics</title>
    @vite('resources/css/app.css')
    @livewireStyles
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
</head>
<body class="antialiased bg-slate-100 text-slate-900">
    <div class="min-h-screen">
        <header class="bg-white shadow">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
                <div>
                    <span class="text-lg font-semibold">{{ config('app.name', 'Laravel') }}</span>
                    <span class="ml-2 text-sm text-slate-500">Admin Analytics</span>
                </div>
                <nav class="flex items-center gap-4 text-sm font-medium">
                    <a class="text-blue-600 hover:text-blue-500" href="{{ route('dashboard') }}">Dashboard</a>
                    <a class="text-blue-600 hover:text-blue-500" href="{{ route('billing.portal') }}">Billing</a>
                    <span class="inline-flex items-center rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-600">
                        Admin
                    </span>
                </nav>
            </div>
        </header>

        <main class="mx-auto max-w-7xl px-6 py-10">
            <div class="relative">
                {{ $slot }}
            </div>
        </main>
    </div>

    @livewireScripts
    @stack('scripts')
</body>
</html>
