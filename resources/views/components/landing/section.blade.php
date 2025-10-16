@props([
    'tagline' => null,
    'title' => null,
    'subtitle' => null,
    'variant' => 'panel',
    'align' => 'left',
    'id' => null,
])

@php
    $alignmentClasses = $align === 'center'
        ? 'mx-auto max-w-3xl text-center'
        : 'max-w-3xl';

    $variantClasses = [
        'panel' => 'rounded-[2.5rem] border border-slate-800/60 bg-slate-950/70 p-8 shadow-xl ring-1 ring-white/5 sm:p-12',
        'contrast' => 'relative overflow-hidden rounded-[2.75rem] border border-emerald-500/20 bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 p-8 shadow-2xl ring-1 ring-emerald-500/20 sm:p-12',
        'plain' => 'p-0',
    ][$variant] ?? 'rounded-[2.5rem] border border-slate-800/60 bg-slate-950/70 p-8 shadow-xl ring-1 ring-white/5 sm:p-12';
@endphp

<section
    @if ($id)
        id="{{ $id }}"
    @endif
    {{ $attributes->class([$variantClasses, 'space-y-10']) }}
>
    @if ($variant === 'contrast')
        <div class="pointer-events-none absolute -top-24 left-16 h-72 w-72 rounded-full bg-emerald-500/15 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-20 right-12 h-80 w-80 rounded-full bg-emerald-400/10 blur-3xl"></div>
    @endif

    @if ($title || $subtitle || $tagline)
        <header class="space-y-3 {{ $alignmentClasses }}">
            @if ($tagline)
                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-emerald-300">{{ $tagline }}</p>
            @endif

            @if ($title)
                <h2 class="text-2xl font-semibold text-white sm:text-3xl lg:text-4xl">{{ $title }}</h2>
            @endif

            @if ($subtitle)
                <p class="text-sm text-slate-300 sm:text-base">{{ $subtitle }}</p>
            @endif
        </header>
    @endif

    <div class="space-y-8 @if ($align === 'center') mx-auto max-w-5xl @endif">
        {{ $slot }}
    </div>
</section>
