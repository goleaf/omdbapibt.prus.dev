@extends('layouts.app', [
    'title' => 'Movie detail',
])

@section('content')
    @livewire('movie-detail', ['movie' => $movie])
@endsection
