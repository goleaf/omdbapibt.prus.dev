@extends('layouts.app', [
    'title' => trans('ui.pages.about.title'),
    'header' => trans('ui.pages.about.heading'),
    'subheader' => trans('ui.pages.about.lede'),
])

@section('content')
    <article class="mx-auto max-w-5xl space-y-12 text-base leading-relaxed text-slate-300">
        <p>{{ trans('ui.pages.about.intro') }}</p>

        @foreach ($sections as $section)
            <section class="space-y-4 rounded-3xl border border-white/5 bg-white/5 p-6 backdrop-blur-sm">
                @if (! empty($section['title']))
                    <h2 class="text-xl font-semibold text-slate-100">{{ $section['title'] }}</h2>
                @endif

                @foreach ($section['paragraphs'] ?? [] as $paragraph)
                    <p>{{ $paragraph }}</p>
                @endforeach

                @if (! empty($section['items']))
                    <ul class="list-disc space-y-2 pl-5 text-sm text-slate-300">
                        @foreach ($section['items'] as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                @endif

                @if (! empty($section['cta']))
                    <a
                        href="{{ $section['cta']['href'] }}"
                        class="inline-flex items-center gap-2 text-sm font-semibold text-emerald-300 transition hover:text-emerald-200"
                    >
                        <span>{{ $section['cta']['label'] }}</span>
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 5h8m0 0v8m0-8L5 15" />
                        </svg>
                    </a>
                @endif
            </section>
        @endforeach
    </article>
@endsection
