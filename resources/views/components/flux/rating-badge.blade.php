@props([
    'score',
    'label' => null,
    'tone' => 'cool',
])

@php
    $displayScore = is_numeric($score) ? number_format((float) $score, 1) : $score;
    $tone = in_array($tone, ['cool', 'warm'], true) ? $tone : 'cool';
@endphp

<span {{ $attributes->class(['rating-badge'])->merge(['data-tone' => $tone === 'warm' ? 'warm' : 'cool']) }}>
    <svg class="h-4 w-4 fill-current" viewBox="0 0 20 20" aria-hidden="true">
        <path d="M10 1.75a.75.75 0 0 1 .68.46l1.74 4.17 4.52.36a.75.75 0 0 1 .42 1.33l-3.42 3.01 1.03 4.4a.75.75 0 0 1-1.11.81L10 14.97l-3.86 2.22a.75.75 0 0 1-1.11-.81l1.03-4.4-3.42-3.01a.75.75 0 0 1 .42-1.33l4.52-.36 1.74-4.17A.75.75 0 0 1 10 1.75Z" />
    </svg>
    <span class="font-semibold">{{ $displayScore }}</span>
    @if ($label)
        <span class="text-xs uppercase tracking-[0.25em]">{{ $label }}</span>
    @endif
</span>
