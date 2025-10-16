@props([
    'label',
    'value',
    'description' => null,
    'icon' => null,
    'trend' => null,
    'trendLabel' => null,
])

@php
    $trendIcon = $trend === 'down' ? 'arrow-down-right' : 'arrow-up-right';
    $trendColor = $trend === 'down' ? 'text-rose-300' : 'text-emerald-300';
@endphp

<dl {{ $attributes->class(['rounded-2xl border border-slate-800/70 bg-slate-950/60 p-5 shadow-inner shadow-slate-950/40']) }}>
    <dt class="flex items-center gap-3 text-xs font-semibold uppercase tracking-[0.35em] text-slate-400">
        @if ($icon)
            <span class="flex size-8 items-center justify-center rounded-xl bg-emerald-500/10 text-emerald-200 ring-1 ring-inset ring-emerald-500/40">
                <flux:icon icon="{{ $icon }}" class="size-4" />
            </span>
        @endif
        <span>{{ $label }}</span>
    </dt>
    <dd class="mt-3 text-3xl font-semibold text-white">{{ $value }}</dd>
    @if ($description)
        <dd class="mt-2 text-sm text-slate-300">{{ $description }}</dd>
    @endif
    @if ($trendLabel)
        <dd class="mt-3 flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.3em] {{ $trendColor }}">
            <span class="inline-flex size-7 items-center justify-center rounded-full bg-white/5">
                <flux:icon icon="{{ $trendIcon }}" class="size-4" />
            </span>
            {{ $trendLabel }}
        </dd>
    @endif
</dl>
