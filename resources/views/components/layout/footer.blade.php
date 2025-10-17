@props([
    'brand' => null,
    'sections' => [],
    'support' => null,
    'links' => [],
    'copyright' => __('ui.nav.footer.copyright', ['year' => now()->year]),
])

@php
    $preparedSections = collect($sections)
        ->map(function ($section) {
            $links = collect(data_get($section, 'links', []))
                ->filter(fn ($link) => filled($link['href'] ?? null) && filled($link['label'] ?? null))
                ->map(function ($link) {
                    return [
                        'label' => $link['label'],
                        'href' => $link['href'],
                        'target' => $link['target'] ?? null,
                        'rel' => $link['rel'] ?? null,
                    ];
                })
                ->values();

            if ($links->isEmpty()) {
                return null;
            }

            return [
                'title' => data_get($section, 'title'),
                'links' => $links,
            ];
        })
        ->filter()
        ->values();

    if ($preparedSections->isEmpty()) {
        $fallbackLinks = collect($links)
            ->filter(fn ($link) => filled($link['href'] ?? null) && filled($link['label'] ?? null))
            ->map(function ($link) {
                return [
                    'label' => $link['label'],
                    'href' => $link['href'],
                    'target' => $link['target'] ?? null,
                    'rel' => $link['rel'] ?? null,
                ];
            })
            ->values();

        if ($fallbackLinks->isNotEmpty()) {
            $preparedSections = collect([
                [
                    'title' => null,
                    'links' => $fallbackLinks,
                ],
            ]);
        }
    }

    $supportBlock = null;

    if (is_array($support) && filled($support['body'] ?? null)) {
        $supportBlock = [
            'title' => $support['title'] ?? null,
            'body' => $support['body'],
            'label' => $support['label'] ?? null,
            'href' => $support['href'] ?? null,
            'email' => $support['email'] ?? null,
        ];
    }

    $brandData = null;

    if (is_array($brand)) {
        $brandData = [
            'primary' => $brand['primary'] ?? config('app.name'),
            'secondary' => $brand['secondary'] ?? null,
            'tagline' => $brand['tagline'] ?? null,
        ];
    }
@endphp

<footer class="surface-shell border-t">
    <div class="mx-auto w-full max-w-screen-2xl px-6 py-12 text-sm text-slate-300 2xl:px-12">
        <div class="grid gap-12 md:grid-cols-[minmax(0,2fr)_minmax(0,3fr)] md:items-start">
            <div class="space-y-6">
                @if ($brandData)
                    <div class="space-y-3">
                        <a href="{{ localized_route('home') }}" class="flex items-center gap-2 text-lg font-semibold tracking-wide text-slate-100">
                            <span class="text-emerald-400">◎</span>
                            <span>
                                {{ $brandData['primary'] }}
                                @if ($brandData['secondary'])
                                    <span class="text-emerald-400">{{ $brandData['secondary'] }}</span>
                                @endif
                            </span>
                        </a>

                        @if (filled($brandData['tagline']))
                            <p class="max-w-md text-sm flux-text-muted">{{ $brandData['tagline'] }}</p>
                        @endif
                    </div>
                @endif

                @if ($supportBlock)
                    <div class="space-y-3 rounded-3xl border border-white/5 bg-white/5 p-5 text-sm leading-relaxed">
                        @if (filled($supportBlock['title']))
                            <p class="text-xs font-semibold uppercase tracking-wide text-emerald-200">{{ $supportBlock['title'] }}</p>
                        @endif

                        <p class="flux-text-muted">
                            @if ($supportBlock['email'] && str_contains($supportBlock['body'], $supportBlock['email']))
                                {!! str_replace(
                                    $supportBlock['email'],
                                    '<a href="mailto:'.$supportBlock['email'].'" class="text-emerald-300 transition hover:text-emerald-200">'.$supportBlock['email'].'</a>',
                                    e($supportBlock['body'])
                                ) !!}
                            @else
                                {{ $supportBlock['body'] }}
                            @endif
                        </p>

                        @if ($supportBlock['href'] && $supportBlock['label'])
                            <a
                                href="{{ $supportBlock['href'] }}"
                                class="inline-flex items-center gap-2 font-semibold text-emerald-300 transition hover:text-emerald-200"
                            >
                                <span>{{ $supportBlock['label'] }}</span>
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 5h8m0 0v8m0-8L5 15" />
                                </svg>
                            </a>
                        @endif
                    </div>
                @endif
            </div>

            @if ($preparedSections->isNotEmpty())
                <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($preparedSections as $section)
                        <div class="space-y-3">
                            @if (! empty($section['title']))
                                <p class="text-xs font-semibold uppercase tracking-wide text-emerald-200">{{ $section['title'] }}</p>
                            @endif

                            <ul class="space-y-2">
                                @foreach ($section['links'] as $link)
                                    <li>
                                        <a
                                            href="{{ $link['href'] }}"
                                            @if ($link['target']) target="{{ $link['target'] }}" @endif
                                            @if ($link['rel']) rel="{{ $link['rel'] }}" @endif
                                            class="transition hover:text-emerald-200"
                                        >
                                            {{ $link['label'] }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="mt-12 border-t border-white/5 pt-6 text-xs flux-text-muted md:flex md:items-center md:justify-between">
            <p>{{ $copyright }}</p>
        </div>
    </div>
</footer>
