@extends('layouts.admin')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.translations.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-500">
            &larr; {{ __('navigation.back_to_translations') }}
        </a>
    </div>

    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <h2 class="text-xl font-semibold">{{ __('admin.create_translation') }}</h2>

        <form method="POST" action="{{ route('admin.translations.store') }}" class="mt-4 space-y-6">
            @csrf

            <div>
                <label for="key" class="block text-sm font-medium text-gray-700">{{ __('admin.key') }}</label>
                <input
                    type="text"
                    id="key"
                    name="key"
                    value="{{ old('key') }}"
                    required
                    class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring"
                >
                @error('key')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid gap-6 md:grid-cols-{{ count($locales) > 1 ? '2' : '1' }}">
                @foreach ($locales as $locale)
                    <div>
                        <label for="text_{{ $locale }}" class="block text-sm font-medium text-gray-700">
                            {{ strtoupper($locale) }}
                        </label>
                        <textarea
                            id="text_{{ $locale }}"
                            name="text[{{ $locale }}]"
                            rows="3"
                            placeholder="{{ __('admin.add_locale_placeholder', ['locale' => strtoupper($locale)]) }}"
                            class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring"
                        >{{ old("text.$locale") }}</textarea>
                        @error("text.$locale")
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                @endforeach
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-500">
                    {{ __('admin.save') }}
                </button>
                <a href="{{ route('admin.translations.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-500">
                    {{ __('admin.cancel') }}
                </a>
            </div>
        </form>
    </div>
@endsection
