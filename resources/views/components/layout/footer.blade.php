@props([
    'copyright' => __('ui.nav.footer.copyright', ['year' => now()->year]),
    'links' => [],
])

@php
    $preparedLinks = collect($links)
        ->filter(fn ($link) => filled($link['href'] ?? null) && filled($link['label'] ?? null))
        ->map(function ($link) {
            return [
                'label' => $link['label'],
                'href' => $link['href'],
                'target' => $link['target'] ?? null,
                'rel' => $link['rel'] ?? null,
            ];
        });
@endphp

<footer class="surface-shell border-t py-8">
    <div class="mx-auto flex w-full max-w-screen-2xl flex-col gap-4 px-6 text-sm flux-text-muted sm:flex-row sm:items-center sm:justify-between 2xl:px-12">
        <p>{{ $copyright }}</p>

        @if ($preparedLinks->isNotEmpty())
            <div class="flex flex-wrap items-center gap-x-4 gap-y-2">
                @foreach ($preparedLinks as $link)
                    <a
                        href="{{ $link['href'] }}"
                        @if ($link['target']) target="{{ $link['target'] }}" @endif
                        @if ($link['rel']) rel="{{ $link['rel'] }}" @endif
                        class="transition hover:text-emerald-300"
                    >
                        {{ $link['label'] }}
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</footer>
