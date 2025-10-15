@extends('layouts.dashboard', [
    'title' => 'Account',
    'header' => 'Account center',
    'subheader' => 'Manage your subscription, preferences, and security options from a single dashboard.',
])

@section('dashboard-content')
    @livewire('account-settings-summary')
@endsection
