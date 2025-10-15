<div class="space-y-8">
    <header class="flex flex-col justify-between gap-6 lg:flex-row lg:items-center">
        <div>
            <h1 class="text-2xl font-semibold text-gray-100">User management directory</h1>
            <p class="mt-2 max-w-2xl text-sm text-gray-400">
                Browse, filter, and export subscriber accounts. Administrators can adjust access levels, launch impersonation
                sessions, and review an immutable activity trail for transparency.
            </p>
        </div>
        <div class="flex flex-col gap-3 text-right text-sm text-gray-400 lg:items-end">
            <button
                wire:click="export"
                type="button"
                class="inline-flex items-center justify-center rounded-lg border border-emerald-500 px-4 py-2 font-semibold text-emerald-300 transition hover:bg-emerald-500/10"
            >
                Export filtered users
            </button>
            <button
                wire:click="clearFilters"
                type="button"
                class="inline-flex items-center justify-center text-xs uppercase tracking-wide text-gray-500 transition hover:text-emerald-300"
            >
                Reset filters
            </button>
        </div>
    </header>

    <section class="rounded-xl border border-gray-800 bg-slate-900/70 p-6 shadow-lg shadow-emerald-500/5">
        <div class="grid gap-4 lg:grid-cols-4">
            <div class="lg:col-span-2">
                <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">Search</label>
                <input
                    type="search"
                    wire:model.live.debounce.400ms="search"
                    placeholder="Search by name or email..."
                    class="mt-2 w-full rounded-lg border border-gray-700 bg-slate-950/60 px-3 py-2 text-sm text-gray-100 focus:border-emerald-400 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                />
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">Role</label>
                <select
                    wire:model="role"
                    class="mt-2 w-full rounded-lg border border-gray-700 bg-slate-950/60 px-3 py-2 text-sm text-gray-100 focus:border-emerald-400 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                >
                    <option value="all">All roles</option>
                    @foreach ($roleOptions as $value => $option)
                        <option value="{{ $value }}">{{ $option['label'] }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">Status</label>
                <select
                    wire:model="status"
                    class="mt-2 w-full rounded-lg border border-gray-700 bg-slate-950/60 px-3 py-2 text-sm text-gray-100 focus:border-emerald-400 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                >
                    <option value="all">All states</option>
                    <option value="verified">Email verified</option>
                    <option value="unverified">Email unverified</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">Sort</label>
                <select
                    wire:model="sort"
                    class="mt-2 w-full rounded-lg border border-gray-700 bg-slate-950/60 px-3 py-2 text-sm text-gray-100 focus:border-emerald-400 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                >
                    <option value="latest">Newest first</option>
                    <option value="oldest">Oldest first</option>
                </select>
            </div>
        </div>
    </section>

    @error('role')
        <div class="rounded-lg border border-red-400 bg-red-500/10 px-4 py-3 text-sm text-red-200">
            {{ $message }}
        </div>
    @enderror

    @error('impersonate')
        <div class="rounded-lg border border-amber-400 bg-amber-500/10 px-4 py-3 text-sm text-amber-100">
            {{ $message }}
        </div>
    @enderror

    <section class="overflow-hidden rounded-xl border border-gray-800 bg-slate-900/70 shadow-lg shadow-emerald-500/5">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-800 text-left text-sm">
                <thead class="bg-slate-900/80 text-xs uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-4 py-3">User</th>
                        <th class="px-4 py-3">Role</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Created</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800">
                    @forelse ($users as $user)
                        <tr wire:key="user-{{ $user->id }}" class="bg-slate-950/40 hover:bg-slate-900/60">
                            <td class="px-4 py-4">
                                <div class="font-semibold text-gray-100">{{ $user->name }}</div>
                                <div class="text-xs text-gray-400">{{ $user->email }}</div>
                            </td>
                            <td class="px-4 py-4">
                                <select
                                    wire:change="updateUserRole({{ $user->id }}, $event.target.value)"
                                    class="w-full rounded-md border border-gray-700 bg-slate-950/60 px-2 py-1 text-xs text-gray-100 focus:border-emerald-400 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                                >
                                    @foreach ($roleOptions as $value => $option)
                                        <option value="{{ $value }}" @selected($user->role->value === $value)>
                                            {{ $option['label'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-4 py-4">
                                <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-medium {{ $user->email_verified_at ? 'bg-emerald-500/10 text-emerald-300' : 'bg-amber-500/10 text-amber-200' }}">
                                    <span class="h-2 w-2 rounded-full {{ $user->email_verified_at ? 'bg-emerald-400' : 'bg-amber-400' }}"></span>
                                    {{ $user->email_verified_at ? 'Verified' : 'Unverified' }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-400">
                                {{ optional($user->created_at)->diffForHumans() }}
                            </td>
                            <td class="px-4 py-4 text-right text-sm">
                                <div class="flex items-center justify-end gap-2">
                                    <button
                                        wire:click="impersonate({{ $user->id }})"
                                        type="button"
                                        class="rounded-md border border-indigo-400 px-3 py-1 text-xs font-semibold text-indigo-200 transition hover:bg-indigo-500/10"
                                        @disabled(! $user->canBeImpersonated())
                                    >
                                        Impersonate
                                    </button>
                                    <a
                                        href="mailto:{{ $user->email }}"
                                        class="rounded-md border border-gray-700 px-3 py-1 text-xs font-semibold text-gray-300 transition hover:border-emerald-400 hover:text-emerald-200"
                                    >
                                        Contact
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-400">
                                No users match the current filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-gray-800 bg-slate-950/50 px-4 py-3">
            {{ $users->links() }}
        </div>
    </section>

    <section class="rounded-xl border border-gray-800 bg-slate-900/70 p-6 shadow-lg shadow-emerald-500/5">
        <h2 class="text-lg font-semibold text-gray-100">Recent administrative activity</h2>
        <p class="mt-1 text-sm text-gray-400">The 10 most recent user management events are recorded for compliance.</p>
        <ul class="mt-4 space-y-3">
            @forelse ($recentLogs as $log)
                <li class="rounded-lg border border-gray-800 bg-slate-950/60 p-4">
                    <div class="flex items-center justify-between text-xs text-gray-500">
                        <span class="font-semibold uppercase tracking-wide text-emerald-300">{{ str_replace('_', ' ', $log['action']) }}</span>
                        <span>{{ $log['performed_at'] }}</span>
                    </div>
                    <div class="mt-2 text-sm text-gray-200">
                        @if ($log['admin'])
                            <p>By {{ $log['admin']['name'] }} ({{ $log['admin']['email'] }})</p>
                        @endif
                        @if ($log['target'])
                            <p class="text-gray-400">Target: {{ $log['target']['name'] }} ({{ $log['target']['email'] }})</p>
                        @endif
                    </div>
                    @if (! empty($log['payload']))
                        <pre class="mt-3 overflow-x-auto rounded-md bg-slate-900/80 p-3 text-xs text-gray-400">{{ json_encode($log['payload'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                    @endif
                </li>
            @empty
                <li class="rounded-lg border border-dashed border-gray-800 p-6 text-center text-sm text-gray-500">
                    No user management actions have been recorded yet.
                </li>
            @endforelse
        </ul>
    </section>
</div>
