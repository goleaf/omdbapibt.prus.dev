@props([
    'iconOnly' => false,
    'label' => 'Toggle theme',
])

<button
    type="button"
    data-theme-toggle
    aria-pressed="false"
    {{ $attributes->class([
        'group relative inline-flex items-center justify-center gap-2 overflow-hidden rounded-full border border-slate-200/60 bg-white/80 px-3 py-1.5 text-sm font-semibold text-slate-700 shadow-sm transition duration-300 hover:border-emerald-300/70 hover:text-emerald-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400/70 dark:border-white/10 dark:bg-slate-900/70 dark:text-slate-200 dark:hover:text-emerald-200',
        'px-2 py-2' => $iconOnly,
    ]) }}
>
    <span
        aria-hidden="true"
        class="pointer-events-none absolute inset-0 bg-gradient-to-r from-emerald-200/30 via-transparent to-cyan-200/30 opacity-0 transition duration-500 ease-out group-hover:opacity-100 dark:from-emerald-500/15 dark:to-cyan-400/15"
    ></span>
    <span class="sr-only">{{ $label }}</span>
    <svg
        class="relative z-10 size-4 text-amber-500 transition dark:hidden"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="1.75"
        aria-hidden="true"
    >
        <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M12 3.75v.75m0 15v.75m8.25-8.25h-.75m-15 0H3.75m14.495 6.495-.53-.53M6.285 6.285l-.53-.53m12.99 0-.53.53m-11.93 11.93-.53.53M12 8.25a3.75 3.75 0 1 1 0 7.5 3.75 3.75 0 0 1 0-7.5Z"
        />
    </svg>
    <svg
        class="relative z-10 hidden size-4 text-emerald-300 transition dark:block"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="1.75"
        aria-hidden="true"
    >
        <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M21 12.79A9 9 0 1 1 11.21 3 7.5 7.5 0 0 0 21 12.79Z"
        />
    </svg>
    @unless ($iconOnly)
        <span class="relative z-10 hidden text-[0.65rem] uppercase tracking-[0.4em] text-slate-500 dark:text-slate-300 md:inline">
            Theme
        </span>
    @endunless
</button>
