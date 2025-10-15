@extends('layouts.app', [
    'title' => 'Flux UI components',
    'header' => 'Flux UI component guide',
    'subheader' => 'Interact with the cinematic presets that power OMDb Stream and copy ready-to-use snippets.',
])

@section('content')
    <div class="space-y-16">
        <section class="grid gap-8 lg:grid-cols-[minmax(0,0.6fr),minmax(0,1fr)]">
            <x-flux.card :interactive="false" padding="p-6">
                <div class="space-y-4">
                    <h2 class="text-2xl font-semibold text-slate-900 dark:text-white">Cinematic foundations</h2>
                    <p class="text-sm text-slate-600 dark:text-slate-300">
                        Tailwind 4 tokens live in <code class="rounded bg-white/60 px-1 py-0.5 text-xs text-slate-700 dark:bg-white/10 dark:text-slate-200">resources/css/app.css</code>.
                        Utilities such as <code class="rounded bg-white/60 px-1 py-0.5 text-xs text-slate-700 dark:bg-white/10 dark:text-slate-200">flux-container</code>
                        and <code class="rounded bg-white/60 px-1 py-0.5 text-xs text-slate-700 dark:bg-white/10 dark:text-slate-200">cinematic-gradient</code>
                        orchestrate gradients, blur, and depth so every screen feels cohesive.
                    </p>
                    <ul class="space-y-2 text-xs font-semibold uppercase tracking-[0.32em] text-slate-500 dark:text-slate-300">
                        <li>Design tokens → colors, radii, shadows, transitions</li>
                        <li>Utilities → cinematic gradient, spotlight, shimmer, float</li>
                        <li>Components → card, rating badge, theme toggle</li>
                    </ul>
                </div>
            </x-flux.card>

            <x-flux.card padding="p-0" class="cinematic-gradient">
                <div class="space-y-6 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.42em] text-slate-500 dark:text-slate-300">Live preview</p>
                            <h3 class="mt-2 text-lg font-semibold text-slate-900 dark:text-white">Hover the card</h3>
                        </div>
                        <span class="rounded-full border border-white/30 px-3 py-1 text-[0.6rem] font-semibold uppercase tracking-[0.35em] text-slate-500 dark:border-white/10 dark:text-slate-200">Interactive</span>
                    </div>
                    <x-flux.card>
                        <div class="space-y-3">
                            <h4 class="text-lg font-semibold text-slate-900 dark:text-white">Featured release</h4>
                            <p class="text-sm text-slate-600 dark:text-slate-300">
                                The Flux card wraps cinematic hover states, ambient overlays, and responsive padding in a single component.
                            </p>
                            <div class="flex flex-wrap gap-2 text-xs font-semibold uppercase tracking-[0.32em] text-slate-500 dark:text-slate-300">
                                <span class="rounded-full border border-slate-300/60 px-3 py-1 dark:border-white/20">Glass</span>
                                <span class="rounded-full border border-slate-300/60 px-3 py-1 dark:border-white/20">Gradient</span>
                                <span class="rounded-full border border-slate-300/60 px-3 py-1 dark:border-white/20">Motion</span>
                            </div>
                        </div>
                    </x-flux.card>
                    <pre class="overflow-auto rounded-2xl border border-white/30 bg-white/70 p-4 text-xs leading-relaxed text-slate-700 backdrop-blur dark:border-white/10 dark:bg-slate-950/70 dark:text-slate-200"><code>&lt;x-flux.card padding=&quot;p-8&quot; eyebrow=&quot;Livewire + Flux UI&quot;&gt;
    &lt;div class=&quot;space-y-4&quot;&gt;
        &lt;h2 class=&quot;text-3xl font-semibold&quot;&gt;Cinematic storytelling&lt;/h2&gt;
        &lt;p class=&quot;text-sm text-slate-600 dark:text-slate-300&quot;&gt;Flux cards handle blur, gradients, and motion out of the box.&lt;/p&gt;
    &lt;/div&gt;
&lt;/x-flux.card&gt;</code></pre>
                </div>
            </x-flux.card>
        </section>

        <section class="grid gap-8 md:grid-cols-2">
            <x-flux.card :interactive="false" padding="p-6" class="space-y-6">
                <div class="space-y-3">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Rating badge</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-300">
                        Flux badges lean on gradients and pill radii. Use them for OMDb, TMDb, or custom scoring contexts.
                        Adjust the slider to preview how the badge responds in real-time.
                    </p>
                </div>
                <div class="space-y-4" data-rating-demo>
                    <div class="flex items-center gap-3">
                        <x-flux.rating-badge :value="8.6" label="Flux" />
                        <span class="text-xs font-semibold uppercase tracking-[0.32em] text-slate-500 dark:text-slate-300">Drag to change score</span>
                    </div>
                    <input
                        type="range"
                        min="0"
                        max="10"
                        step="0.1"
                        value="8.6"
                        class="w-full accent-emerald-400"
                        data-rating-slider
                    >
                </div>
                <pre class="overflow-auto rounded-2xl border border-white/30 bg-white/70 p-4 text-xs leading-relaxed text-slate-700 backdrop-blur dark:border-white/10 dark:bg-slate-950/70 dark:text-slate-200"><code>&lt;x-flux.rating-badge :value=&quot;9.1&quot; label=&quot;Critics&quot; /&gt;</code></pre>
            </x-flux.card>

            <x-flux.card :interactive="false" padding="p-6" class="space-y-6">
                <div class="space-y-3">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Theme toggle</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-300">
                        The toggle persists the chosen mode using <code class="rounded bg-white/60 px-1 py-0.5 text-xs text-slate-700 dark:bg-white/10 dark:text-slate-200">localStorage</code>
                        and respects the system preference. Drop it into any layout or page-level toolbar.
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <x-flux.theme-toggle />
                    <x-flux.theme-toggle icon-only />
                </div>
                <pre class="overflow-auto rounded-2xl border border-white/30 bg-white/70 p-4 text-xs leading-relaxed text-slate-700 backdrop-blur dark:border-white/10 dark:bg-slate-950/70 dark:text-slate-200"><code>&lt;x-flux.theme-toggle class=&quot;hidden sm:inline-flex&quot; /&gt;</code></pre>
            </x-flux.card>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const demos = document.querySelectorAll('[data-rating-demo]');

            demos.forEach((demo) => {
                const slider = demo.querySelector('[data-rating-slider]');
                const badge = demo.querySelector('[data-rating-badge]');
                const valueTarget = demo.querySelector('[data-rating-value]');

                if (!slider || !badge || !valueTarget) {
                    return;
                }

                const scale = Number(badge.dataset.ratingScale ?? 10);

                const update = (next) => {
                    const formatted = Number(next).toFixed(1);
                    valueTarget.textContent = formatted;
                    badge.dataset.ratingValue = formatted;
                    badge.setAttribute('title', `${formatted} out of ${scale}`);
                };

                slider.addEventListener('input', (event) => update(event.target.value));
                update(slider.value);
            });
        });
    </script>
@endpush
