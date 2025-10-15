@extends('layouts.app', [
    'title' => __('Person detail'),
])

@section('content')
    @livewire('person-detail', ['identifier' => $person])
@endsection
