@extends('layouts.app', [
    'title' => 'Series detail',
])

@section('content')
    @livewire('show-detail-overview', ['slug' => $slug])
@endsection
