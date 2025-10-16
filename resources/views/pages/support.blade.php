@extends('layouts.app', [
    'title' => __('support.page.meta.title'),
    'header' => __('support.page.header'),
    'subheader' => __('support.page.subheader'),
])

@section('content')
    <section class="mx-auto max-w-3xl">
        <livewire:support.support-form />
    </section>
@endsection
