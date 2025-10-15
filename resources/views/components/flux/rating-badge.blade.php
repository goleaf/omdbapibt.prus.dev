@props([
    'value' => null,
    'scale' => 10,
    'size' => 'md',
    'label' => null,
])

@php
    $displayValue = is_null($value) ? 'NR' : number_format((float) $value, 1);

    $sizeClasses = match ($size) {
        'sm' => 'text-xs px-2 py-1 gap-1',
        'lg' => 'text-base px-3.5 py-2 gap-2',
        default => 'text-sm px-2.5 py-1.5 gap-1.5',
    };

    $title = is_null($value) ? 'Not yet rated' : sprintf('%s out of %s', $displayValue, $scale);
@endphp

<span
    {{ $attributes->class([
        'inline-flex items-center rounded-full bg-gradient-to-r from-amber-400/80 via-orange-500/80 to-rose-500/80 font-semibold uppercase tracking-[0.25em] text-amber-50 shadow-[var(--shadow-glow)] ring-1 ring-inset ring-white/30 dark:ring-white/15',
        $sizeClasses,
    ]) }}
    role="status"
    title="{{ $title }}"
    data-rating-badge
    data-rating-scale="{{ $scale }}"
>
    <svg class="size-4 shrink-0 text-amber-100 dark:text-amber-200" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
        <path d="M12 2.25 14.782 8.3l6.468.543-4.932 4.18 1.486 6.707L12 16.974l-5.804 2.756 1.486-6.707-4.932-4.18 6.468-.542L12 2.25Z" />
    </svg>
    <span class="font-semibold tracking-normal text-slate-900 dark:text-white" data-rating-value>
        {{ $displayValue }}
        @if (! is_null($value))
            <span class="text-[0.65rem] font-medium uppercase tracking-[0.3em] text-slate-900/70 dark:text-slate-200/70">/{{ $scale }}</span>
        @endif
    </span>
    @if ($label)
        <span class="hidden text-[0.6rem] font-semibold tracking-[0.35em] text-amber-100/80 sm:inline">
            {{ $label }}
        </span>
    @endif
</span>
