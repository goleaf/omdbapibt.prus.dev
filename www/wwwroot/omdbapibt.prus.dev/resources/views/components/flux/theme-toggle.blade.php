<button
    type="button"
    data-theme-toggle
    {{ $attributes->class(['flux-button'])->merge(['data-variant' => 'ghost']) }}
>
    <span class="flex items-center gap-2 text-sm font-medium">
        <svg class="h-4 w-4" aria-hidden="true" viewBox="0 0 24 24" fill="currentColor">
            <path
                d="M12 2.5a1 1 0 0 1 1 1v1.02A7.5 7.5 0 0 1 19.48 12H20.5a1 1 0 1 1 0 2h-1.02A7.5 7.5 0 0 1 13 19.48V20.5a1 1 0 1 1-2 0v-1.02A7.5 7.5 0 0 1 4.52 14H3.5a1 1 0 1 1 0-2h1.02A7.5 7.5 0 0 1 11 4.52V3.5a1 1 0 0 1 1-1Zm0 4a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11Z"
            />
        </svg>
        <span data-theme-label class="uppercase tracking-wide">Dark mode</span>
    </span>
</button>
