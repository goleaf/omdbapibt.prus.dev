@extends('layouts.app', [
    'title' => trans('ui.pages.terms.title'),
    'header' => trans('ui.pages.terms.heading'),
    'subheader' => trans('ui.pages.terms.lede'),
])

@section('content')
    @php($supportEmail = config('support.contact_email', config('mail.from.address', 'support@omdbstream.test')))
    @php($sections = trans('ui.pages.terms.sections'))

    <article class="mx-auto max-w-4xl space-y-10 text-sm leading-relaxed text-slate-300">
        <p>{{ trans('ui.pages.terms.intro') }}</p>

        @foreach ($sections as $section)
            <section class="space-y-3">
                <h2 class="text-xl font-semibold text-slate-100">{{ $section['title'] }}</h2>

                @foreach ($section['paragraphs'] as $paragraph)
                    <p>{{ $paragraph }}</p>
                @endforeach
            </section>
        @endforeach

        <section class="space-y-3 border-t border-white/5 pt-6">
            <h2 class="text-xl font-semibold text-slate-100">{{ trans('ui.pages.terms.contact.title') }}</h2>
            <p>{{ trans('ui.pages.terms.contact.body', ['email' => $supportEmail]) }}</p>
            <p class="text-xs uppercase tracking-widest text-slate-500">{{ trans('ui.pages.terms.effective_date') }}</p>
        </section>
    </article>
@endsection
