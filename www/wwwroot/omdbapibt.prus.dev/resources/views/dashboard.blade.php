@extends('layouts.app')

@section('title', config('app.name', 'Laravel') . ' — Dashboard')
@php($pageTitle = 'Dashboard')

@section('content')
    <section class="rounded-lg bg-white p-6 shadow">
        <h2 class="text-xl font-semibold mb-2">Welcome back!</h2>
        <p class="text-gray-600">
            Access your Stripe billing portal to update payment methods, review invoices,
            or cancel your subscription at any time using the navigation above.
        </p>
    </section>
@endsection
