<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }} &mdash; {{ __('navigation.ui_translations') }}</title>
    @vite('resources/css/app.css')
</head>
<body class="antialiased bg-gray-100 text-gray-900">
    <div class="min-h-screen">
        <header class="bg-white shadow">
            <div class="mx-auto max-w-7xl px-6 py-4 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold">{{ __('navigation.ui_translations') }}</h1>
                    <p class="text-sm text-gray-600">{{ __('admin.translations_description') }}</p>
                </div>
                <nav class="flex flex-wrap items-center gap-4 text-sm font-medium">
                    <a class="text-blue-600 hover:text-blue-500" href="{{ route('dashboard') }}">
                        {{ __('navigation.back_to_dashboard') }}
                    </a>
                </nav>
            </div>
        </header>

        <main class="mx-auto max-w-7xl px-6 py-10">
            @if (session('status'))
                <div class="mb-6 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</body>
</html>
