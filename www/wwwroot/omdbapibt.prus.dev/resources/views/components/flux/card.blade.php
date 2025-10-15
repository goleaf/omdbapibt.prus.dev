@props([
    'title',
    'meta' => null,
    'image' => null,
    'rating' => null,
    'href' => '#',
    'tag' => null,
    'highlight' => false,
])

<article {{ $attributes->class(['flux-card group']) }}>
    <a href="{{ $href }}" class="relative block overflow-hidden">
        <div class="relative overflow-hidden">
            <img
                src="{{ $image }}"
                alt="Poster for {{ $title }}"
                class="flux-card__poster"
                loading="lazy"
            />
            @if ($rating)
                <div class="absolute left-5 top-5">
                    <x-flux.rating-badge :score="$rating" :variant="$highlight ? 'highlight' : 'default'" label="IMDb" />
                </div>
            @endif
            @if ($tag)
                <span
                    class="absolute bottom-4 right-4 inline-flex items-center rounded-full bg-brand-500/90 px-4 py-1 text-xs font-semibold uppercase tracking-wide text-white shadow-lg shadow-brand-500/40"
                >
                    {{ $tag }}
                </span>
            @endif
        </div>
    </a>
    <div class="flux-card__body">
        <div class="flex items-start justify-between gap-3">
            <div>
                <h3 class="text-lg font-semibold text-white drop-shadow-lg">{{ $title }}</h3>
                @if ($meta)
                    <p class="text-sm text-surface-200/80">{{ $meta }}</p>
                @endif
            </div>
            <a
                href="{{ $href }}"
                class="mt-1 inline-flex items-center gap-1 rounded-full border border-white/10 px-3 py-1 text-xs font-semibold text-white/80 transition hover:border-white/40 hover:text-white"
            >
                View
                <svg aria-hidden="true" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4">
                    <path
                        fill-rule="evenodd"
                        d="M5.22 4.22a.75.75 0 0 1 1.06 0l5.47 5.47-5.47 5.47a.75.75 0 1 1-1.06-1.06L9.94 9.5 5.22 4.78a.75.75 0 0 1 0-1.06Z"
                        clip-rule="evenodd"
                    />
                </svg>
            </a>
        </div>
        <p class="text-sm text-surface-100/70">
            Discover trailers, watch providers, and curated recommendations powered by Flux UI.
        </p>
    </div>
</article>
