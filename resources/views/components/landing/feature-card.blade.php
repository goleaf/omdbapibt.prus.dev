@props([
    'icon' => null,
    'title',
    'description' => null,
    'badge' => null,
])

<article {{ $attributes->class(['group relative flex flex-col gap-4 rounded-3xl border border-slate-800/70 bg-slate-950/60 p-6 shadow-sm transition duration-200 hover:border-emerald-400/60 hover:shadow-xl']) }}>
    <div class="flex items-start gap-4">
        @if ($icon)
            <span class="flex size-12 items-center justify-center rounded-2xl bg-emerald-500/10 text-emerald-200 ring-1 ring-inset ring-emerald-500/40">
                <flux:icon icon="{{ $icon }}" class="size-6" />
            </span>
        @endif

        <div class="space-y-2">
            <div class="flex items-center gap-3">
                <h3 class="text-lg font-semibold text-white">{{ $title }}</h3>
                @if ($badge)
                    <span class="inline-flex items-center rounded-full border border-emerald-400/40 bg-emerald-500/10 px-2.5 py-1 text-[11px] font-semibold uppercase tracking-[0.25em] text-emerald-200">{{ $badge }}</span>
                @endif
            </div>
            @if ($description)
                <p class="text-sm leading-relaxed text-slate-300">{{ $description }}</p>
            @endif
            @if (trim($slot) !== '')
                <div class="text-xs uppercase tracking-[0.3em] text-emerald-200/70 group-hover:text-emerald-200">{{ $slot }}</div>
            @endif
        </div>
    </div>
</article>
