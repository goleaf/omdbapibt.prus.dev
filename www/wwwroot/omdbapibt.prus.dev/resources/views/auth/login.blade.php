<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }} &mdash; {{ __('auth.login_title') }}</title>
    @vite('resources/css/app.css')
</head>
<body class="antialiased bg-gray-100 text-gray-900">
    <div class="flex min-h-screen flex-col items-center justify-center px-6 py-12">
        <div class="w-full max-w-md rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <h1 class="text-2xl font-semibold text-center">{{ __('auth.login_title') }}</h1>
            <p class="mt-4 text-sm text-gray-600 text-center">{{ __('auth.login_placeholder_copy') }}</p>
        </div>
    </div>
</body>
</html>
