<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }} &mdash; Dashboard</title>
    @if (app()->environment('testing'))
        <style>
            body { font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; }
        </style>
    @else
        @vite('resources/css/app.css')
    @endif
</head>
<body class="antialiased bg-gray-100 text-gray-900">
    <div class="min-h-screen">
        <header class="bg-white shadow">
            <div class="mx-auto max-w-7xl px-6 py-4 flex items-center justify-between">
                <h1 class="text-2xl font-semibold">Dashboard</h1>
                <nav class="flex items-center gap-4 text-sm font-medium">
                    <a class="text-blue-600 hover:text-blue-500" href="{{ route('dashboard') }}">Overview</a>
                    <a class="text-blue-600 hover:text-blue-500" href="{{ route('billing.portal') }}">
                        Manage Subscription
                    </a>
                </nav>
            </div>
        </header>

        <main class="mx-auto max-w-7xl px-6 py-10 space-y-6">
            <section class="rounded-lg bg-white p-6 shadow">
                <h2 class="text-xl font-semibold mb-2">Welcome back!</h2>
                <p class="text-gray-600">
                    Access your Stripe billing portal to update payment methods, review invoices,
                    or cancel your subscription at any time using the link above.
                </p>
            </section>
        </main>
    </div>
</body>
</html>
