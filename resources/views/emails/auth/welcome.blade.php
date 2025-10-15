@component('mail::message')
# {{ __('Welcome to OMDb Stream') }}

{{ __('Hi :name, thanks for joining OMDb Stream. Your account is ready to explore our catalog of movies and shows.', ['name' => $user->name]) }}

{{ __('Happy streaming!') }}  
{{ config('app.name', 'OMDb Stream') }}
@endcomponent
