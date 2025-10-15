@extends('layouts.app', [
    'title' => __('ui.people.page_title'),
])

@section('content')
    @livewire('person-detail', ['person' => $person])
@endsection
