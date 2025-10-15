@props([
    'as' => 'div',
    'padding' => 'p-6',
    'interactive' => true,
    'orientation' => 'vertical',
    'media' => null,
    'eyebrow' => null,
])

@php
    $tag = $as;
    $isHorizontal = $orientation === 'horizontal';
    $contentLayout = $isHorizontal
        ? 'grid gap-6 lg:grid-cols-[minmax(0,0.55fr),minmax(0,1fr)] lg:items-center'
        : 'space-y-5';
@endphp

<{{ $tag }}
    {{ $attributes
        ->merge([
            'data-static-card' => $interactive === false ? 'true' : null,
        ])
        ->class([
            'group flux-panel overflow-hidden text-base text-slate-900 dark:text-slate-100',
            $padding,
        ]) }}
>
    @if ($interactive)
        <span
            aria-hidden="true"
            class="pointer-events-none absolute inset-0 opacity-0 transition duration-700 ease-out group-hover:opacity-80"
        >
            <span
                class="absolute inset-x-8 top-[-40%] h-52 rounded-full bg-gradient-to-r from-emerald-400/35 via-cyan-400/40 to-blue-500/30 blur-3xl"
            ></span>
            <span class="absolute inset-0 bg-white/20 mix-blend-overlay dark:bg-white/5"></span>
        </span>
    @endif

    <div class="relative z-10 space-y-6">
        @if ($media)
            <div class="overflow-hidden rounded-[calc(var(--radius-card)_-_0.75rem)] border border-white/20 shadow-[0_35px_70px_-45px_rgba(15,23,42,0.65)] dark:border-white/10">
                {{ $media }}
            </div>
        @endif

        <div class="space-y-4">
            @if ($eyebrow)
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500 dark:text-slate-400">
                    {{ $eyebrow }}
                </p>
            @endif

            <div class="{{ $contentLayout }}">
                {{ $slot }}
            </div>
        </div>
    </div>
</{{ $tag }}>
