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

<footer class="relative mt-20 overflow-hidden border-t border-[color:var(--flux-border-soft)] bg-gradient-to-b from-transparent to-[color:var(--flux-surface-base)]">
    <div class="absolute inset-0 opacity-30">
        <div class="absolute top-0 left-1/4 h-64 w-64 rounded-full bg-emerald-500/10 blur-3xl"></div>
        <div class="absolute top-0 right-1/4 h-64 w-64 rounded-full bg-blue-500/10 blur-3xl"></div>
    </div>
    
    <div class="relative mx-auto w-full max-w-screen-2xl px-6 py-12 2xl:px-12">
        <div class="flex flex-col gap-8 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex flex-col gap-3">
                <p class="text-sm font-medium text-[color:var(--flux-text-muted)]">{{ $copyright }}</p>
                <p class="text-xs text-[color:var(--flux-text-muted)] opacity-70">Built with Laravel, Livewire & Tailwind CSS</p>
            </div>

            @if ($preparedLinks->isNotEmpty())
                <div class="flex flex-wrap items-center gap-x-6 gap-y-3">
                    @foreach ($preparedLinks as $link)
                        <a
                            href="{{ $link['href'] }}"
                            @if ($link['target']) target="{{ $link['target'] }}" @endif
                            @if ($link['rel']) rel="{{ $link['rel'] }}" @endif
                            class="group relative text-sm font-medium text-[color:var(--flux-text-muted)] transition-colors duration-300 hover:text-emerald-400"
                        >
                            <span class="relative z-10">{{ $link['label'] }}</span>
                            <span class="absolute inset-x-0 -bottom-1 h-0.5 origin-left scale-x-0 rounded-full bg-gradient-to-r from-emerald-400 to-emerald-600 transition-transform duration-300 group-hover:scale-x-100"></span>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</footer>
