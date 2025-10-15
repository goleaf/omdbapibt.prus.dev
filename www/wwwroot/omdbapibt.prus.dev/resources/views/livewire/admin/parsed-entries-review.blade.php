<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Laravel') }} — Parser Review</title>
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <style>
                :root {
                    color-scheme: light;
                    font-family: 'Instrument Sans', ui-sans-serif, system-ui, -apple-system,
                        BlinkMacSystemFont, 'Segoe UI', sans-serif;
                }

                body {
                    margin: 0;
                    background-color: #f1f5f9;
                    color: #0f172a;
                }

                *,
                *::before,
                *::after {
                    box-sizing: border-box;
                }
            </style>
        @endif
        @livewireStyles
    </head>
    <body class="bg-slate-100 text-slate-900">
        <div class="min-h-screen">
            <header class="bg-white shadow">
                <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-5">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-widest text-slate-500">Admin</p>
                        <h1 class="text-2xl font-bold">Parser Moderation Queue</h1>
                        <p class="text-sm text-slate-500">Review newly parsed records, compare changes, and publish decisions.</p>
                    </div>
                    <a href="{{ route('dashboard') }}" class="text-sm font-medium text-blue-600 hover:text-blue-500">Back to dashboard</a>
                </div>
            </header>

            <main class="mx-auto max-w-7xl px-6 py-10">
                <div class="grid gap-6 lg:grid-cols-[420px,1fr]">
                    <section class="space-y-4">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-semibold text-slate-800">Recently Parsed</h2>
                            <label class="text-xs font-medium text-slate-500">
                                Status
                                <select wire:model.live="statusFilter" class="mt-1 block rounded-md border border-slate-200 bg-white py-1.5 pl-2 pr-8 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">
                                    @foreach ($this->statusOptions() as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </label>
                        </div>

                        <div class="space-y-3">
                            @forelse ($entries as $entry)
                                <button
                                    type="button"
                                    wire:click="selectEntry({{ $entry->id }})"
                                    class="w-full rounded-xl border border-slate-200 bg-white p-4 text-left shadow-sm transition hover:border-blue-200 hover:shadow"
                                >
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <p class="text-sm font-semibold text-slate-800">{{ $this->summaryForEntry($entry) }}</p>
                                            <p class="text-xs text-slate-500">Submitted {{ $entry->created_at?->diffForHumans() ?? '—' }}</p>
                                        </div>
                                        <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">
                                            {{ ucfirst($entry->status) }}
                                        </span>
                                    </div>
                                    @php($diff = $entry->diff())
                                    <div class="mt-3 flex flex-wrap gap-2 text-xs">
                                        @if (count($diff['updated']))
                                            <span class="rounded-full bg-amber-100 px-2 py-1 font-medium text-amber-700">{{ count($diff['updated']) }} updated</span>
                                        @endif
                                        @if (count($diff['added']))
                                            <span class="rounded-full bg-emerald-100 px-2 py-1 font-medium text-emerald-700">{{ count($diff['added']) }} added</span>
                                        @endif
                                        @if (count($diff['removed']))
                                            <span class="rounded-full bg-rose-100 px-2 py-1 font-medium text-rose-700">{{ count($diff['removed']) }} removed</span>
                                        @endif
                                    </div>
                                </button>
                            @empty
                                <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-6 text-center text-sm text-slate-500">
                                    No entries match the selected status.
                                </div>
                            @endforelse
                        </div>

                        <div>
                            {{ $entries->links() }}
                        </div>
                    </section>

                    <section class="space-y-6">
                        @if ($this->selectedEntry)
                            @php($selectedDiff = $this->selectedEntry->diff())
                            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                                <div class="flex flex-wrap items-center justify-between gap-4">
                                    <div>
                                        <h2 class="text-xl font-semibold text-slate-800">{{ $this->summaryForEntry($this->selectedEntry) }}</h2>
                                        <p class="text-sm text-slate-500">Status: <span class="font-medium text-slate-700">{{ $this->selectedEntry->statusLabel() }}</span></p>
                                    </div>
                                    <div class="flex gap-2">
                                        <button wire:click="approveEntry({{ $this->selectedEntry->id }})" class="rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-emerald-500">Approve &amp; Publish</button>
                                        <button wire:click="rejectEntry({{ $this->selectedEntry->id }})" class="rounded-md bg-rose-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-rose-500">Reject</button>
                                    </div>
                                </div>

                                <div class="mt-6 space-y-5">
                                    <article>
                                        <h3 class="text-sm font-semibold uppercase tracking-wider text-slate-500">Change summary</h3>
                                        <div class="mt-3 space-y-4">
                                            @include('livewire.admin.partials.diff-section', ['title' => 'Updated fields', 'items' => $selectedDiff['updated'], 'type' => 'updated'])
                                            @include('livewire.admin.partials.diff-section', ['title' => 'New fields', 'items' => $selectedDiff['added'], 'type' => 'added'])
                                            @include('livewire.admin.partials.diff-section', ['title' => 'Removed fields', 'items' => $selectedDiff['removed'], 'type' => 'removed'])
                                        </div>
                                    </article>

                                    <article class="space-y-3">
                                        <h3 class="text-sm font-semibold uppercase tracking-wider text-slate-500">Moderator comment</h3>
                                        <div>
                                            <textarea
                                                wire:model.defer="comment"
                                                rows="3"
                                                class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                                placeholder="Leave additional context for this decision"
                                            ></textarea>
                                            @error('comment')
                                                <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <button
                                                wire:click="addComment({{ $this->selectedEntry->id }})"
                                                class="text-sm font-semibold text-blue-600 hover:text-blue-500"
                                            >
                                                Add comment
                                            </button>
                                            <p class="text-xs text-slate-400">Comments are logged in the moderation history.</p>
                                        </div>
                                    </article>

                                    <article>
                                        <h3 class="text-sm font-semibold uppercase tracking-wider text-slate-500">History</h3>
                                        <ul class="mt-3 space-y-3">
                                            @forelse ($this->selectedEntry->histories as $history)
                                                <li class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                                                    <div class="flex items-start justify-between gap-3">
                                                        <div>
                                                            <p class="text-sm font-semibold text-slate-700">{{ ucfirst($history->action) }}</p>
                                                            @if ($history->notes)
                                                                <p class="mt-1 text-sm text-slate-600">{{ $history->notes }}</p>
                                                            @endif
                                                        </div>
                                                        <p class="text-xs text-slate-500 text-right">
                                                            {{ $history->user?->name ?? 'System' }}
                                                            <br>
                                                            {{ $history->created_at->diffForHumans() }}
                                                        </p>
                                                    </div>
                                                </li>
                                            @empty
                                                <li class="rounded-lg border border-dashed border-slate-200 bg-slate-50 p-4 text-sm text-slate-500">
                                                    No moderation history yet.
                                                </li>
                                            @endforelse
                                        </ul>
                                    </article>
                                </div>
                            </div>
                        @else
                            <div class="flex h-full items-center justify-center rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center">
                                <div class="max-w-sm space-y-3">
                                    <h2 class="text-lg font-semibold text-slate-700">Select an entry to begin review</h2>
                                    <p class="text-sm text-slate-500">Choose a parsed record from the queue to inspect its changes, discuss with other moderators, and publish or reject it.</p>
                                </div>
                            </div>
                        @endif
                    </section>
                </div>
            </main>
        </div>

        @livewireScripts
    </body>
</html>
