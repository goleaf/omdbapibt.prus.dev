@props([
    'label' => __('ui.nav.theme_toggle'),
])

<button
    type="button"
    data-theme-toggle
    class="theme-toggle"
    aria-live="polite"
>
    <span class="flex items-center gap-2">
        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-theme-icon="dark">
            <path d="M10 2.5a.75.75 0 0 1 .75.75V5a.75.75 0 1 1-1.5 0V3.25A.75.75 0 0 1 10 2.5Zm5.657 1.843a.75.75 0 0 1 1.06 1.06l-1.24 1.24a.75.75 0 1 1-1.06-1.06l1.24-1.24ZM10 6.5a3.5 3.5 0 1 1 0 7 3.5 3.5 0 0 1 0-7Zm7.5 3.5a.75.75 0 0 1-.75.75H16a.75.75 0 0 1 0-1.5h.75a.75.75 0 0 1 .75.75Zm-2.033 5.657a.75.75 0 0 1-1.06 0l-1.24-1.24a.75.75 0 0 1 1.06-1.06l1.24 1.24a.75.75 0 0 1 0 1.06ZM10 15a.75.75 0 0 1 .75.75V17a.75.75 0 0 1-1.5 0v-1.25A.75.75 0 0 1 10 15ZM5.303 4.343a.75.75 0 0 1 0 1.06l-1.24 1.24a.75.75 0 0 1-1.06-1.06l1.24-1.24a.75.75 0 0 1 1.06 0ZM4 10a.75.75 0 0 1-.75.75H2a.75.75 0 0 1 0-1.5h1.25A.75.75 0 0 1 4 10Zm1.303 4.657-1.24 1.24a.75.75 0 0 1-1.06-1.06l1.24-1.24a.75.75 0 0 1 1.06 1.06Z" />
        </svg>
        <svg class="hidden h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-theme-icon="light">
            <path d="M10 2.25c.478 0 .95.044 1.409.13a.75.75 0 0 1 .226 1.38A6.254 6.254 0 0 0 10 3.75c-3.175 0-5.75 2.575-5.75 5.75s2.575 5.75 5.75 5.75a6.255 6.255 0 0 0 4.24-1.624.75.75 0 0 1 1.162.944A7.752 7.752 0 0 1 10 17.25c-4.28 0-7.75-3.47-7.75-7.75S5.72 1.75 10 1.75Z" />
        </svg>
        <span
            data-theme-label
            data-label-dark="{{ __('ui.nav.theme.dark') }}"
            data-label-light="{{ __('ui.nav.theme.light') }}"
        >
            {{ $label }}
        </span>
    </span>
</button>
