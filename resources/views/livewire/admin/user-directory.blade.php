<div class="space-y-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div class="flex flex-1 items-center gap-3">
            <div class="flex-1">
                <label class="text-xs uppercase tracking-[0.35em] text-slate-400" for="user-search">Search</label>
                <input
                    id="user-search"
                    type="search"
                    wire:model.live="search"
                    placeholder="Filter by name or email"
                    class="mt-1 w-full rounded-full border border-slate-700 bg-slate-900/80 px-4 py-2 text-sm text-slate-100 focus:border-emerald-400 focus:outline-none"
                />
            </div>
            <div>
                <label class="text-xs uppercase tracking-[0.35em] text-slate-400" for="role-filter">Role</label>
                <select
                    id="role-filter"
                    wire:model.live="roleFilter"
                    class="mt-1 rounded-full border border-slate-700 bg-slate-900/80 px-3 py-2 text-sm text-slate-100 focus:border-emerald-400 focus:outline-none"
                >
                    <option value="">All roles</option>
                    @foreach ($this->roles as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <button
                type="button"
                wire:click="exportCsv"
                class="rounded-full border border-slate-700 px-4 py-2 text-sm text-slate-100 transition hover:border-emerald-400 hover:text-emerald-200"
            >
                Export CSV
            </button>
        </div>
    </div>

    <div class="overflow-hidden rounded-3xl border border-slate-800/60 bg-slate-900/70">
        <table class="min-w-full divide-y divide-slate-800 text-sm">
            <thead class="bg-slate-900/80 text-slate-400">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left">User</th>
                    <th scope="col" class="px-6 py-3 text-left">Role</th>
                    <th scope="col" class="px-6 py-3 text-left">Watch history</th>
                    <th scope="col" class="px-6 py-3 text-left">Joined</th>
                    <th scope="col" class="px-6 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800 text-slate-200">
                @forelse ($this->users as $user)
                    <tr class="hover:bg-slate-900/60">
                        <td class="px-6 py-4">
                            <div class="font-semibold text-white">{{ $user->name }}</div>
                            <div class="text-xs text-slate-400">{{ $user->email }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <select
                                wire:change="updateRole({{ $user->id }}, $event.target.value)"
                                class="rounded-full border border-slate-700 bg-slate-900/80 px-3 py-1 text-xs text-slate-100 focus:border-emerald-400 focus:outline-none"
                            >
                                @foreach ($this->roles as $value => $label)
                                    <option value="{{ $value }}" @selected($user->role?->value === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="px-6 py-4">{{ $user->watch_histories_count }}</td>
                        <td class="px-6 py-4">{{ optional($user->created_at)->toDateTimeString() }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                @if ($this->canImpersonateUser($user))
                                    <button
                                        type="button"
                                        wire:click="impersonate({{ $user->id }})"
                                        class="rounded-full border border-slate-700 px-3 py-1 text-xs text-slate-100 transition hover:border-emerald-400 hover:text-emerald-200"
                                    >
                                        Impersonate
                                    </button>
                                @elseif ($this->impersonating)
                                    <span class="text-xs text-emerald-200">Impersonation active</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-400">No users found for the selected filters.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $this->users->links() }}
    </div>
</div>
