@php
    $cardExample = <<<'BLADE'
<x-flux.card
    title="Nebula Run"
    meta="2024 • 2h 12m • Sci-Fi Adventure"
    image="https://image.tmdb.org/t/p/w500/1syW9SNna38rSl9fnXwc9fP7POW.jpg"
    rating="8.4"
    tag="New"
    :highlight="true"
/>
BLADE;

    $ratingExample = <<<'BLADE'
<x-flux.rating-badge score="97" label="Audience" variant="highlight" />
BLADE;
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Flux UI Components • Guide</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-surface-50 font-sans text-surface-900 dark:bg-surface-950 dark:text-surface-100">
        <div class="mx-auto flex max-w-6xl flex-col gap-16 px-6 pb-24 pt-16">
            <header class="space-y-6 text-center">
                <p class="inline-flex items-center gap-2 rounded-full border border-surface-200/50 px-4 py-2 text-xs font-semibold uppercase tracking-[0.4em] text-surface-500 dark:border-surface-800 dark:text-surface-400">
                    Flux UI Reference
                </p>
                <h1 class="text-4xl font-bold text-balance sm:text-5xl">Reusable building blocks for movie dashboards</h1>
                <p class="mx-auto max-w-3xl text-lg text-surface-600 dark:text-surface-300">
                    This guide documents the Tailwind + Flux theme tokens introduced in <span class="font-semibold">tailwind.config.js</span>, along with the Netflix-style cards and IMDb-inspired rating badges available as Blade components.
                </p>
                <div class="flex flex-wrap items-center justify-center gap-4">
                    <a href="{{ url('/') }}" class="flux-button">Back to landing</a>
                    <x-flux.theme-toggle />
                </div>
            </header>

            <section id="cards" class="space-y-6" data-story data-default-tab="preview">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="space-y-2">
                        <h2 class="text-2xl font-semibold">Flux Card</h2>
                        <p class="text-sm text-surface-500 dark:text-surface-300">
                            Displays artwork, metadata, a CTA, and an optional IMDb rating badge with contextual accent colours.
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="rounded-full bg-brand-500/10 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-brand-500">
                            Netflix-inspired layout
                        </span>
                        <span class="rounded-full bg-imdb/20 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-surface-900 dark:text-surface-100">
                            Glass morphic
                        </span>
                    </div>
                </div>
                <div class="flux-story">
                    <div class="flux-story__tabs">
                        <button type="button" data-story-tab="preview">Preview</button>
                        <button type="button" data-story-tab="code">Code</button>
                    </div>
                    <div class="flux-story__panel" data-story-panel="preview">
                        <div class="mx-auto max-w-sm">
                            {!! $cardExample !!}
                        </div>
                    </div>
                    <div class="flux-story__panel" data-story-panel="code" hidden>
                        <pre><code>{{ $cardExample }}</code></pre>
                    </div>
                </div>
                <div class="rounded-3xl bg-surface-100/50 p-6 text-sm leading-6 text-surface-600 dark:bg-surface-900/60 dark:text-surface-300">
                    <ul class="list-disc space-y-2 pl-6">
                        <li><span class="font-semibold">Props:</span> <code>title</code>, <code>meta</code>, <code>image</code>, <code>rating</code>, <code>tag</code>, and <code>highlight</code> to toggle the neon IMDb badge.</li>
                        <li>Wrap the component in Livewire <code>\Livewire\withPagination</code> loops to ship responsive grids without writing HTML.</li>
                        <li>Composes with Flux data lists, carousels, and command palette components without additional styling.</li>
                    </ul>
                </div>
            </section>

            <section class="space-y-6" data-story>
                <div class="space-y-2">
                    <h2 class="text-2xl font-semibold">Rating Badge</h2>
                    <p class="text-sm text-surface-500 dark:text-surface-300">
                        Inspired by IMDb, this badge uses the <code>imdb</code> token with automatic dark mode contrast and an optional highlight variant.
                    </p>
                </div>
                <div class="flux-story">
                    <div class="flux-story__tabs">
                        <button type="button" data-story-tab="preview">Preview</button>
                        <button type="button" data-story-tab="code">Code</button>
                    </div>
                    <div class="flux-story__panel" data-story-panel="preview">
                        <div class="flex flex-wrap items-center gap-4">
                            <x-flux.rating-badge score="8.9" label="IMDb" />
                            <x-flux.rating-badge score="97" label="Audience" variant="highlight" />
                            <x-flux.rating-badge score="92" label="Critics" />
                        </div>
                    </div>
                    <div class="flux-story__panel" data-story-panel="code" hidden>
                        <pre><code>{{ $ratingExample }}</code></pre>
                    </div>
                </div>
                <div class="rounded-3xl bg-surface-100/50 p-6 text-sm leading-6 text-surface-600 dark:bg-surface-900/60 dark:text-surface-300">
                    <ul class="list-disc space-y-2 pl-6">
                        <li>Swap <code>label</code> text to represent providers (IMDb, Rotten Tomatoes, in-app metrics).</li>
                        <li>Use <code>variant="highlight"</code> for Netflix-red accenting on hero placements.</li>
                        <li>Works with Flux badge groups, tables, and scoreboard components.</li>
                    </ul>
                </div>
            </section>

            <section class="space-y-4 rounded-3xl bg-gradient-to-br from-brand-500/10 via-surface-100/60 to-imdb/20 p-8 text-sm leading-6 text-surface-700 dark:from-brand-500/20 dark:via-surface-900/60 dark:to-imdb/30 dark:text-surface-200">
                <h2 class="text-2xl font-semibold">Implementation checklist</h2>
                <ol class="list-decimal space-y-2 pl-6">
                    <li>Include <code>@vite(['resources/css/app.css', 'resources/js/app.js'])</code> in layouts to compile the Tailwind 4 + Flux tokens.</li>
                    <li>Use the <code>flux-button</code> utility class or <code>&lt;x-flux.theme-toggle /&gt;</code> for consistent CTAs with dark mode state awareness.</li>
                    <li>Reference <code>tailwind.config.js</code> to extend theme palettes (<code>brand</code>, <code>surface</code>, <code>imdb</code>) for additional UI patterns.</li>
                </ol>
            </section>
        </div>
    </body>
</html>
