<div class="space-y-8" wire:poll.10s="refresh">
    <header class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Parser &amp; Queue Control</h1>
            <p class="mt-1 text-sm text-gray-500">
                Monitor Horizon queues, dispatch parser jobs, and review administrative activity.
            </p>
        </div>
        <div class="text-right text-sm text-gray-500">
            <p class="font-medium text-gray-700">Last updated</p>
            <p>{{ $lastUpdated ?: '—' }}</p>
        </div>
    </header>

    <section>
        <h2 class="text-lg font-semibold text-gray-800">Dispatch parser commands</h2>
        <div class="mt-4 grid gap-4 md:grid-cols-3">
            @foreach ($commands as $signature => $label)
                <button
                    wire:click="queueCommand('{{ $signature }}')"
                    class="inline-flex w-full items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-3 text-left text-gray-800 shadow-sm transition hover:border-indigo-400 hover:text-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    <span class="font-medium">{{ $label }}</span>
                    <span class="text-xs text-gray-500">{{ $signature }}</span>
                </button>
            @endforeach
        </div>
        @if (! empty($queuedCommands))
            <div class="mt-4 rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-800">
                <h3 class="font-semibold">Recently queued commands</h3>
                <ul class="mt-2 space-y-1">
                    @foreach (array_slice(array_reverse($queuedCommands), 0, 5) as $queued)
                        <li class="flex items-center justify-between">
                            <span>{{ data_get($commands, $queued['command'], $queued['command']) }}</span>
                            <span class="text-xs text-green-700">Queued at {{ $queued['queued_at'] }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </section>

    <section class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-4 lg:col-span-2">
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-800">Horizon statistics</h2>
                <dl class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-6">
                    <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                        <dt class="text-sm text-gray-500">Jobs per minute</dt>
                        <dd class="mt-1 text-2xl font-semibold text-gray-800">{{ number_format((float) data_get($stats, 'jobsPerMinute', 0), 1) }}</dd>
                    </div>
                    <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                        <dt class="text-sm text-gray-500">Jobs processed</dt>
                        <dd class="mt-1 text-2xl font-semibold text-gray-800">{{ number_format((int) data_get($stats, 'jobsProcessed', 0)) }}</dd>
                    </div>
                    <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                        <dt class="text-sm text-gray-500">Jobs failed</dt>
                        <dd class="mt-1 text-2xl font-semibold text-gray-800">{{ number_format((int) data_get($stats, 'jobsFailed', 0)) }}</dd>
                    </div>
                    <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                        <dt class="text-sm text-gray-500">Status</dt>
                        <dd class="mt-1 text-xl font-semibold text-gray-800">{{ data_get($stats, 'status', 'Unknown') }}</dd>
                    </div>
                    <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                        <dt class="text-sm text-gray-500">Active workers</dt>
                        <dd class="mt-1 text-2xl font-semibold text-gray-800">{{ number_format((int) data_get($stats, 'activeWorkers', 0)) }}</dd>
                    </div>
                    <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                        <dt class="text-sm text-gray-500">Longest wait</dt>
                        @php
                            $longestWait = data_get($stats, 'longestWait');
                        @endphp
                        <dd class="mt-1 text-2xl font-semibold text-gray-800">
                            {{ $longestWait ? number_format((int) data_get($longestWait, 'seconds', 0)) . 's' : '—' }}
                        </dd>
                        @if ($longestWait)
                            <dd class="text-xs text-gray-500">Queue {{ data_get($longestWait, 'queue', '—') }}</dd>
                        @endif
                    </div>
                </dl>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-800">Queue workload</h2>
                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                        <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500">
                            <tr>
                                <th class="px-4 py-2">Queue</th>
                                <th class="px-4 py-2">Pending jobs</th>
                                <th class="px-4 py-2">Wait (s)</th>
                                <th class="px-4 py-2">Workers</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse ($workload as $queue)
                                <tr>
                                    <td class="px-4 py-2 font-medium text-gray-800">{{ $queue['queue'] }}</td>
                                    <td class="px-4 py-2 text-gray-700">{{ $queue['length'] }}</td>
                                    <td class="px-4 py-2 text-gray-700">{{ $queue['wait'] }}</td>
                                    <td class="px-4 py-2 text-gray-700">{{ $queue['processes'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-4 text-center text-gray-500">No Horizon queue data available.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-800">Recent jobs</h2>
                <ul class="mt-4 space-y-3">
                    @forelse ($recentJobs as $job)
                        <li class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-semibold text-gray-800">{{ $job['name'] }}</p>
                                    <p class="text-xs text-gray-500">Queue: {{ $job['queue'] }}</p>
                                </div>
                                <span class="rounded-full bg-indigo-100 px-3 py-1 text-xs font-medium text-indigo-700">
                                    {{ $job['status'] }}
                                </span>
                            </div>
                            <div class="mt-2 grid gap-2 text-xs text-gray-500 sm:grid-cols-2">
                                <span>Completed: {{ $job['completed_at'] ?: '—' }}</span>
                                <span>Failed: {{ $job['failed_at'] ?: '—' }}</span>
                            </div>
                        </li>
                    @empty
                        <li class="rounded-lg border border-dashed border-gray-200 p-6 text-center text-gray-500">
                            No recent jobs recorded by Horizon.
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>

        <aside class="space-y-4">
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-800">Audit trail</h2>
                <p class="mt-1 text-sm text-gray-500">Latest administrative actions are recorded for transparency.</p>
                <ul class="mt-4 space-y-3 text-sm">
                    @forelse ($auditLogEntries as $log)
                        <li class="rounded-lg border border-gray-100 bg-gray-50 p-3">
                            <p class="font-medium text-gray-800">{{ $log['action'] }}</p>
                            <p class="text-xs text-gray-500">{{ data_get($log['details'], 'command') }}</p>
                            <p class="mt-1 text-xs text-gray-400">{{ $log['created_at'] }}</p>
                            @if ($log['user'])
                                <p class="mt-1 text-xs text-gray-500">By {{ $log['user']['name'] }} ({{ $log['user']['email'] }})</p>
                            @endif
                        </li>
                    @empty
                        <li class="rounded-lg border border-dashed border-gray-200 p-6 text-center text-gray-500">
                            No administrative actions logged yet.
                        </li>
                    @endforelse
                </ul>
            </div>
        </aside>
    </section>
</div>
