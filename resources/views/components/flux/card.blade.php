@props([
    'heading' => null,
    'subheading' => null,
    'elevated' => false,
])

@php
    $classes = collect(['flux-card', 'group']);

    if ($elevated) {
        $classes->push('shadow-emerald-500/20');
    }
@endphp

<div {{ $attributes->class($classes->all())->merge(['data-elevated' => $elevated ? 'true' : 'false']) }}>
    @if ($heading || $subheading || isset($actions))
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                @if ($heading)
                    <h3 class="flux-card__heading">{{ $heading }}</h3>
                @endif

                @if ($subheading)
                    <p class="flux-card__subheading">{{ $subheading }}</p>
                @endif
            </div>

            @isset($actions)
                <div class="flux-card__actions">
                    {{ $actions }}
                </div>
            @endisset
        </div>
    @endif

    <div class="mt-6 space-y-4">
        {{ $slot }}
    </div>

    @isset($footer)
        <div class="mt-6 border-t border-slate-800/40 pt-4 text-sm text-slate-400">
            {{ $footer }}
        </div>
    @endisset
</div>
