@extends('layouts.app', [
    'title' => 'Movie detail',
])

@section('content')
    @livewire('movie-detail-overview', ['slug' => $slug])
@endsection
