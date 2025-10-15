<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }} &mdash; {{ __('navigation.dashboard') }}</title>
    @vite('resources/css/app.css')
</head>
<body class="antialiased bg-gray-100 text-gray-900">
    <div class="min-h-screen">
        <header class="bg-white shadow">
            <div class="mx-auto max-w-7xl px-6 py-4 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <h1 class="text-2xl font-semibold">{{ __('navigation.dashboard') }}</h1>
                <nav class="flex flex-wrap items-center gap-4 text-sm font-medium">
                    <a class="text-blue-600 hover:text-blue-500" href="{{ route('dashboard') }}">
                        {{ __('navigation.overview') }}
                    </a>
                    <a class="text-blue-600 hover:text-blue-500" href="{{ route('billing.portal') }}">
                        {{ __('navigation.manage_subscription') }}
                    </a>
                    @if (Route::has('admin.translations.index'))
                        <a class="text-blue-600 hover:text-blue-500" href="{{ route('admin.translations.index') }}">
                            {{ __('navigation.ui_translations') }}
                        </a>
                    @endif
                </nav>
            </div>
        </header>

        <main class="mx-auto max-w-7xl px-6 py-10 space-y-6">
            <section class="rounded-lg bg-white p-6 shadow">
                <h2 class="text-xl font-semibold mb-2">{{ __('dashboard.welcome_heading') }}</h2>
                <p class="text-gray-600">
                    {{ __('dashboard.welcome_copy') }}
                </p>
            </section>

            @if (Route::has('admin.translations.index'))
                <section class="rounded-lg bg-white p-6 shadow">
                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                        <div>
                            <h2 class="text-xl font-semibold">{{ __('dashboard.translation_summary_title') }}</h2>
                            <p class="text-gray-600">{{ __('dashboard.translation_summary_copy') }}</p>
                        </div>
                        <a class="inline-flex items-center justify-center rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-500"
                           href="{{ route('admin.translations.index') }}">
                            {{ __('dashboard.open_translation_manager') }}
                        </a>
                    </div>
                </section>
            @endif
        </main>
    </div>
</body>
</html>
