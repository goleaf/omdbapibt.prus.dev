@extends('layouts.app', [
    'title' => $title ?? __('ui.dashboard.title'),
    'header' => $header ?? __('ui.dashboard.layout.default_header'),
    'subheader' => $subheader ?? null,
])

@section('content')
    <div class="grid gap-8 lg:grid-cols-[18rem,1fr]">
        <aside class="space-y-4">
            <div class="rounded-3xl border border-slate-800/60 bg-slate-900/60 p-5">
                <h2 class="text-sm font-semibold uppercase tracking-wider text-slate-400">{{ __('ui.dashboard.layout.sidebar_heading') }}</h2>
                <nav class="mt-4 space-y-2">
                    @foreach (($navigation ?? []) as $item)
                        <a
                            href="{{ $item['href'] ?? '#' }}"
                            @if (! empty($item['target'])) target="{{ $item['target'] }}" rel="noopener" @endif
                            @class([
                                'group block rounded-2xl border px-4 py-3 transition',
                                'border-emerald-500/60 bg-emerald-500/10 text-emerald-200' => (bool) ($item['active'] ?? false),
                                'border-slate-800/70 bg-slate-900/40 text-slate-300 hover:border-emerald-400/70 hover:bg-emerald-500/10 hover:text-emerald-200' => ! ($item['active'] ?? false),
                            ])
                        >
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-semibold">{{ $item['label'] }}</span>
                                <span aria-hidden="true" class="text-xs text-slate-500 transition group-hover:text-emerald-200">→</span>
                            </div>
                            @if (! empty($item['description']))
                                <p class="mt-1 text-xs text-slate-400 group-hover:text-slate-300">
                                    {{ $item['description'] }}
                                </p>
                            @endif
                        </a>
                    @endforeach
                </nav>
            </div>
        </aside>

        <div class="space-y-6">
            @if (session('status'))
                <div class="rounded-xl border border-emerald-500/40 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-100">
                    {{ session('status') }}
                </div>
            @endif

            @if (session('error'))
                <div class="rounded-xl border border-rose-500/40 bg-rose-500/10 px-4 py-3 text-sm text-rose-100">
                    {{ session('error') }}
                </div>
            @endif

            {{ $slot ?? '' }}
        </div>
    </div>
@endsection
