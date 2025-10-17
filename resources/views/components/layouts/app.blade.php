@php
    $layoutData = get_defined_vars();
    unset($layoutData['__data'], $layoutData['__path'], $layoutData['slot']);
@endphp

@extends('layouts.app', $layoutData)

@section('content')
    {{ $slot }}
@endsection
