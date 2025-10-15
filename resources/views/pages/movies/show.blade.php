@extends('layouts.app', [
    'title' => 'Movie detail',
])

@section('content')
    @livewire('movie-detail', ['movie' => $slug])
@endsection
