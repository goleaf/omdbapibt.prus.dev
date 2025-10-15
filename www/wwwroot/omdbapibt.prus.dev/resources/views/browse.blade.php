<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }} &mdash; Browse</title>
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
                <h1 class="text-2xl font-semibold">Browse Catalog</h1>
                <a class="text-blue-600 hover:text-blue-500" href="{{ route('dashboard') }}">Dashboard</a>
            </div>
        </header>

        <main class="mx-auto max-w-7xl px-6 py-10 space-y-6">
            <section class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @forelse ($movies as $movie)
                    <article class="rounded-lg bg-white p-6 shadow">
                        <h2 class="text-lg font-semibold">{{ $movie->title }}</h2>
                        <p class="text-sm text-gray-600">Popularity: {{ number_format($movie->popularity ?? 0, 1) }}</p>
                        <p class="mt-3 text-gray-700">{{ Str::limit($movie->plot, 120) }}</p>
                    </article>
                @empty
                    <p class="text-gray-600">No movies available yet. Run the parser command to import data.</p>
                @endforelse
            </section>
        </main>
    </div>
</body>
</html>
