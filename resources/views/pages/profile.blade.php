@extends('layouts.app', [
    'title' => __('account.profile.meta.title'),
    'header' => __('account.profile.meta.header'),
    'subheader' => __('account.profile.meta.subheader'),
])

@section('content')
    @livewire('account.profile-page')
@endsection
