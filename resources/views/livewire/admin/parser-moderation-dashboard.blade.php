<div class="space-y-8" wire:poll.30s="refreshEntries">
    <header class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Parser Moderation</h1>
            <p class="mt-1 text-sm text-gray-500">
                Review parsed payloads, compare changes, and decide whether to promote or reject them.
            </p>
        </div>
        @if ($selectedEntry)
            <div class="text-sm text-gray-500">
                <p class="font-medium text-gray-700">Current status</p>
                <p class="mt-0.5 inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-gray-700">
                    {{ Str::headline($selectedEntry->status->value) }}
                </p>
            </div>
        @endif
    </header>

    <div class="grid gap-6 lg:grid-cols-3">
        <aside class="space-y-4">
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-800">Queue</h2>
                <p class="mt-1 text-sm text-gray-500">Pending entries awaiting moderation.</p>
                <ul class="mt-4 space-y-2">
                    @forelse ($entries as $entry)
                        <li>
                            <button
                                wire:click="selectEntry({{ $entry->id }})"
                                class="w-full rounded-lg border px-3 py-2 text-left text-sm transition focus:outline-none focus:ring-2 focus:ring-indigo-500 {{ $selectedEntry && $selectedEntry->id === $entry->id ? 'border-indigo-400 bg-indigo-50 text-indigo-700' : 'border-gray-200 bg-white text-gray-700 hover:border-indigo-200 hover:text-indigo-600' }}"
                            >
                                @php
                                    $entryTitle = data_get($entry->payload, 'title');

                                    if (is_array($entryTitle)) {
                                        $entryTitle = $entryTitle[app()->getLocale()] ?? $entryTitle['en'] ?? reset($entryTitle) ?? 'Untitled entry';
                                    }

                                    if (! is_string($entryTitle) || $entryTitle === '') {
                                        $entryTitle = 'Untitled entry';
                                    }
                                @endphp
                                <div class="flex items-center justify-between">
                                    <span class="font-medium">{{ $entryTitle }}</span>
                                    <span class="text-xs text-gray-500">{{ ucfirst($entry->status->value) }}</span>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Parser: {{ $entry->parser }}</p>
                                <p class="text-xs text-gray-400">Submitted {{ $entry->created_at->diffForHumans() }}</p>
                            </button>
                        </li>
                    @empty
                        <li class="rounded-lg border border-dashed border-gray-200 p-4 text-center text-sm text-gray-500">
                            No parser entries are awaiting review.
                        </li>
                    @endforelse
                </ul>
            </div>
        </aside>

        <main class="space-y-6 lg:col-span-2">
            @if (! $selectedEntry)
                <div class="rounded-xl border border-dashed border-gray-200 bg-white p-12 text-center text-gray-500">
                    Select an entry from the queue to inspect the parsed payload and review its history.
                </div>
            @else
                <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900">Parsed payload diff</h2>
                            <p class="mt-1 text-sm text-gray-500">
                                Differences are calculated against the captured baseline snapshot or the current database record.
                            </p>
                        </div>
                        @if ($selectedEntry->reviewer)
                            <div class="text-sm text-gray-500">
                                <p class="font-medium text-gray-700">Reviewed by</p>
                                <p>{{ $selectedEntry->reviewer->name }}</p>
                                <p class="text-xs text-gray-400">{{ optional($selectedEntry->reviewed_at)->toDayDateTimeString() }}</p>
                            </div>
                        @endif
                    </div>

                    <div class="mt-6 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                            <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500">
                                <tr>
                                    <th class="px-4 py-3">Field</th>
                                    <th class="px-4 py-3">Baseline</th>
                                    <th class="px-4 py-3">Parsed</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @forelse ($diff as $change)
                                    <tr>
                                        <td class="px-4 py-3 font-medium text-gray-800">{{ $change['key'] }}</td>
                                        <td class="px-4 py-3 text-gray-600">
                                            <pre class="whitespace-pre-wrap break-words text-xs">{{ json_encode($change['before'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                        </td>
                                        <td class="px-4 py-3 text-gray-800">
                                            <pre class="whitespace-pre-wrap break-words text-xs text-green-700">{{ json_encode($change['after'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-4 py-6 text-center text-sm text-gray-500">
                                            No differences detected. The parsed payload matches the baseline snapshot.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <h2 class="text-xl font-semibold text-gray-900">Decision</h2>
                    <p class="mt-1 text-sm text-gray-500">Leave context for your decision. Notes are required when rejecting an entry.</p>
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700" for="decision-notes">Notes</label>
                        <textarea
                            id="decision-notes"
                            wire:model.defer="decisionNotes"
                            rows="4"
                            class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            placeholder="Summarize why this payload should be approved or rejected"
                        ></textarea>
                        @error('decisionNotes')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center">
                        <button
                            wire:click="approve"
                            class="inline-flex items-center justify-center rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                        >
                            Approve and persist
                        </button>
                        <button
                            wire:click="reject"
                            class="inline-flex items-center justify-center rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                        >
                            Reject entry
                        </button>
                    </div>
                </section>

                <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-gray-900">Review history</h2>
                    <p class="mt-1 text-sm text-gray-500">Chronological log of actions taken on this entry.</p>
                    <ul class="mt-4 space-y-3">
                        @forelse ($history as $event)
                            <li class="rounded-lg border border-gray-100 bg-gray-50 p-4 text-sm text-gray-700">
                                <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                                    <span class="font-semibold text-gray-800">{{ Str::headline($event->action->value) }}</span>
                                    <span class="text-xs text-gray-500">{{ $event->created_at->toDayDateTimeString() }}</span>
                                </div>
                                @if ($event->user)
                                    <p class="text-xs text-gray-500">By {{ $event->user->name }} ({{ $event->user->email }})</p>
                                @endif
                                @if ($event->notes)
                                    <p class="mt-2 text-sm text-gray-600">Notes: {{ $event->notes }}</p>
                                @endif
                                @if (! empty($event->changes))
                                    <details class="mt-2 text-xs text-gray-500">
                                        <summary class="cursor-pointer font-medium text-gray-600">View captured diff</summary>
                                        <pre class="mt-2 whitespace-pre-wrap break-words rounded bg-white/70 p-3">{{ json_encode($event->changes, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                    </details>
                                @endif
                            </li>
                        @empty
                            <li class="rounded-lg border border-dashed border-gray-200 p-6 text-center text-sm text-gray-500">
                                No history recorded yet. Decisions will appear here once taken.
                            </li>
                        @endforelse
                    </ul>
                </section>
            @endif
        </main>
    </div>
</div>
