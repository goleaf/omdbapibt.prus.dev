@extends('layouts.app', [
    'title' => trans('ui.pages.privacy.title'),
    'header' => trans('ui.pages.privacy.heading'),
    'subheader' => trans('ui.pages.privacy.lede'),
])

@section('content')
    <article class="mx-auto max-w-4xl space-y-10 text-sm leading-relaxed text-slate-300">
        <p>{{ trans('ui.pages.privacy.intro') }}</p>

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
            </section>
        @endforeach

        <section class="space-y-3 border-t border-white/5 pt-6">
            <h2 class="text-xl font-semibold text-slate-100">{{ trans('ui.pages.privacy.contact.title') }}</h2>
            <p>{{ trans('ui.pages.privacy.contact.body', ['email' => $supportEmail]) }}</p>
            <p class="text-xs uppercase tracking-widest text-slate-500">{{ trans('ui.pages.privacy.effective_date') }}</p>
        </section>
    </article>
@endsection
