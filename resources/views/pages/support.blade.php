@extends('layouts.app', [
    'title' => trans('ui.pages.support.title'),
    'header' => trans('ui.pages.support.heading'),
    'subheader' => trans('ui.pages.support.lede'),
])

@section('content')
    <article class="mx-auto max-w-4xl space-y-10 text-sm leading-relaxed text-slate-300">
        <p>{{ trans('ui.pages.support.intro') }}</p>

        @foreach ($sections as $section)
            <section class="space-y-3">
                @if (! empty($section['title']))
                    <h2 class="text-xl font-semibold text-slate-100">{{ $section['title'] }}</h2>
                @endif

                @foreach ($section['paragraphs'] as $paragraph)
                    <p>{{ $paragraph }}</p>
                @endforeach

                @if ($section['items'] !== [])
                    <ul class="list-disc space-y-2 pl-5">
                        @foreach ($section['items'] as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                @endif

                @if ($section['cta'])
                    <a
                        href="{{ $section['cta']['href'] }}"
                        class="inline-flex items-center gap-2 text-sm font-semibold text-emerald-300 transition hover:text-emerald-200"
                    >
                        {{ $section['cta']['label'] }}
                    </a>
                @endif
            </section>
        @endforeach

        <section class="space-y-3 border-t border-white/5 pt-6">
            <h2 class="text-xl font-semibold text-slate-100">{{ trans('ui.pages.support.contact.title') }}</h2>
            <p>{{ trans('ui.pages.support.contact.body', ['email' => $supportEmail]) }}</p>
        </section>
    </article>
@endsection
