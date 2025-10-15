<div class="space-y-6">
    <section class="rounded-lg bg-white p-6 shadow">
        <h2 class="text-lg font-semibold mb-4">Filter watch history</h2>

        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            <label class="flex flex-col gap-1">
                <span class="text-sm font-medium text-gray-700">Search title</span>
                <input
                    type="search"
                    wire:model.debounce.400ms="search"
                    placeholder="Search by title..."
                    class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
            </label>

            <label class="flex flex-col gap-1">
                <span class="text-sm font-medium text-gray-700">Status</span>
                <select
                    wire:model.live="status"
                    class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
                    <option value="all">All</option>
                    <option value="completed">Completed</option>
                    <option value="in_progress">In progress</option>
                </select>
            </label>

            <label class="flex flex-col gap-1">
                <span class="text-sm font-medium text-gray-700">Content type</span>
                <select
                    wire:model.live="contentType"
                    class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
                    <option value="all">All</option>
                    <option value="movie">Movies</option>
                    <option value="tv">TV Shows</option>
                </select>
            </label>

            <label class="flex flex-col gap-1">
                <span class="text-sm font-medium text-gray-700">Timeframe</span>
                <select
                    wire:model.live="period"
                    class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
                    <option value="all">All time</option>
                    <option value="7">Last 7 days</option>
                    <option value="30">Last 30 days</option>
                    <option value="90">Last 90 days</option>
                    <option value="365">Last year</option>
                </select>
            </label>
        </div>

        <div class="mt-4 flex items-center justify-between">
            <p class="text-sm text-gray-500">
                Showing {{ $histories->total() }} {{ \Illuminate\Support\Str::plural('record', $histories->total()) }}.
            </p>
            <label class="flex items-center gap-2 text-sm text-gray-700">
                <span>Per page</span>
                <select
                    wire:model.live="perPage"
                    class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
                    <option value="15">15</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
            </label>
        </div>
    </section>

    <section class="rounded-lg bg-white shadow">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 font-medium text-gray-600">Title</th>
                        <th class="px-4 py-3 font-medium text-gray-600">Type</th>
                        <th class="px-4 py-3 font-medium text-gray-600">Status</th>
                        <th class="px-4 py-3 font-medium text-gray-600">Progress</th>
                        <th class="px-4 py-3 font-medium text-gray-600">Viewed at</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($histories as $history)
                        <tr>
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-900">{{ $history->content_title }}</div>
                                <div class="text-xs text-gray-500 truncate">
                                    {{ class_basename($history->watchable_type) }}
                                </div>
                            </td>
                            <td class="px-4 py-3 capitalize text-gray-700">
                                {{ str_replace('_', ' ', $history->content_type) }}
                            </td>
                            <td class="px-4 py-3 text-gray-700">
                                {{ ucwords(str_replace('_', ' ', $history->status)) }}
                            </td>
                            <td class="px-4 py-3 text-gray-700">
                                @if (! is_null($history->progress_percent))
                                    {{ $history->progress_percent }}%
                                @elseif(! is_null($history->duration_seconds))
                                    {{ __('Unknown') }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-700">
                                {{ optional($history->viewed_at)->format('M d, Y H:i') ?? '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500">
                                No watch history found for the selected filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-gray-200 px-4 py-3">
            {{ $histories->links() }}
        </div>
    </section>
</div>
