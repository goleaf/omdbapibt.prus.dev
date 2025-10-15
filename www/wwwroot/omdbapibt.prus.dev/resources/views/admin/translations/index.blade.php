@extends('layouts.admin')

@section('content')
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <form method="GET" action="{{ route('admin.translations.index') }}" class="flex w-full max-w-xl items-center gap-2">
            <input
                type="search"
                name="search"
                value="{{ $search }}"
                placeholder="{{ __('filters.search_placeholder') }}"
                class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring"
            >
            <button type="submit" class="inline-flex items-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow hover:bg-blue-500">
                {{ __('filters.apply_filters') }}
            </button>
        </form>

        <a href="{{ route('admin.translations.create') }}" class="inline-flex items-center justify-center rounded-md bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-green-500">
            {{ __('admin.create_translation') }}
        </a>
    </div>

    <div class="mt-6 overflow-hidden rounded-lg border border-gray-200 bg-white">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-700">{{ __('admin.key') }}</th>
                    @foreach ($locales as $locale)
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">{{ strtoupper($locale) }}</th>
                    @endforeach
                    <th class="px-4 py-3 text-right font-semibold text-gray-700">&nbsp;</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($translations as $translation)
                    <tr>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $translation->key }}</td>
                        @foreach ($locales as $locale)
                            <td class="px-4 py-3 text-gray-600">
                                {{ $translation->getTranslation('text', $locale, false) ?? '—' }}
                            </td>
                        @endforeach
                        <td class="px-4 py-3 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.translations.edit', $translation) }}" class="inline-flex items-center rounded-md border border-blue-200 px-3 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-50">
                                    {{ __('navigation.edit_translation') }}
                                </a>
                                <form method="POST" action="{{ route('admin.translations.destroy', $translation) }}" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center rounded-md border border-red-200 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-50">
                                        {{ __('admin.delete') }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($locales) + 2 }}" class="px-4 py-6 text-center text-sm text-gray-500">
                            {{ __('admin.no_translations') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $translations->links() }}
    </div>
@endsection
