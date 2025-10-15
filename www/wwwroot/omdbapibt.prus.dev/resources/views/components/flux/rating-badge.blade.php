@props([
    'score' => null,
    'label' => null,
    'variant' => 'default',
])

@php
    $variant = $variant === 'highlight' ? 'highlight' : 'default';
@endphp

<span {{ $attributes->class(['flux-rating'])->merge(['data-variant' => $variant]) }}>
    <svg aria-hidden="true" viewBox="0 0 24 24" fill="currentColor" class="opacity-90">
        <path
            d="M12 3.2c.3 0 .6.2.7.5l1.8 4.9 5.3.2c.3 0 .6.2.7.6a.7.7 0 0 1-.2.7l-4.1 3.3 1.5 5a.7.7 0 0 1-.3.8.8.8 0 0 1-.8 0L12 16.7l-4.6 2.5a.8.8 0 0 1-.8 0 .7.7 0 0 1-.3-.8l1.5-5L3.7 10c-.2-.2-.3-.5-.2-.7.1-.4.4-.6.7-.6l5.3-.2 1.8-4.9c.1-.3.4-.5.7-.5Z"
        />
    </svg>
    <span class="text-sm font-semibold">{{ number_format((float) $score, 1) }}</span>
    @if ($label)
        <span class="hidden text-xs font-medium uppercase tracking-wide text-surface-800 sm:inline dark:text-surface-100">
            {{ $label }}
        </span>
    @endif
</span>
