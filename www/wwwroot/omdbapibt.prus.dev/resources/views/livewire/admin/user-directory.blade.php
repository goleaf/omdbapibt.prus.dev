<div class="space-y-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div class="grid w-full grid-cols-1 gap-4 md:grid-cols-4">
            <div class="md:col-span-2">
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                    {{ __('Search users') }}
                </label>
                <input
                    id="search"
                    type="search"
                    wire:model.live.debounce.500ms="search"
                    class="mt-1 w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                    placeholder="{{ __('Search by name or email') }}"
                />
            </div>

            <div>
                <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                    {{ __('Role') }}
                </label>
                <select
                    id="role"
                    wire:model.live="role"
                    class="mt-1 w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                >
                    <option value="">{{ __('All roles') }}</option>
                    @foreach ($this->roleOptions as $roleOption)
                        <option value="{{ $roleOption }}">{{ \Illuminate\Support\Str::headline($roleOption) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="plan" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                    {{ __('Subscription plan') }}
                </label>
                <select
                    id="plan"
                    wire:model.live="plan"
                    class="mt-1 w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                >
                    <option value="">{{ __('All plans') }}</option>
                    <option value="none">{{ __('No active subscription') }}</option>
                    @foreach ($this->availablePlans as $availablePlan)
                        <option value="{{ $availablePlan }}">{{ \Illuminate\Support\Str::headline($availablePlan) }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="flex w-full flex-col items-stretch gap-2 md:w-auto md:flex-row md:items-center md:justify-end">
            <div>
                <label for="perPage" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                    {{ __('Per page') }}
                </label>
                <select
                    id="perPage"
                    wire:model.live="perPage"
                    class="mt-1 w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                >
                    @foreach ($perPageOptions as $option)
                        <option value="{{ $option }}">{{ $option }}</option>
                    @endforeach
                </select>
            </div>

            <button
                type="button"
                wire:click="export('csv')"
                class="inline-flex items-center justify-center rounded-md border border-indigo-500 px-4 py-2 text-sm font-semibold text-indigo-600 transition hover:bg-indigo-50 dark:border-indigo-400 dark:text-indigo-300 dark:hover:bg-indigo-400/10"
            >
                {{ __('Export CSV') }}
            </button>

            @if (session()->has('impersonator_id'))
                <button
                    type="button"
                    wire:click="stopImpersonating"
                    class="inline-flex items-center justify-center rounded-md border border-rose-500 px-4 py-2 text-sm font-semibold text-rose-600 transition hover:bg-rose-50 dark:border-rose-400 dark:text-rose-300 dark:hover:bg-rose-400/10"
                >
                    {{ __('Stop impersonation') }}
                </button>
            @endif
        </div>
    </div>

    <div class="overflow-hidden rounded-lg border border-gray-200 shadow-sm dark:border-gray-700">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">
                        {{ __('User') }}
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">
                        {{ __('Role') }}
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">
                        {{ __('Subscription') }}
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">
                        {{ __('Status') }}
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">
                        {{ __('Actions') }}
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-900">
                @forelse ($this->users as $user)
                    @php
                        /** @var \App\Models\User $user */
                        $subscription = $user->currentSubscription() ?? $user->subscriptions->first();
                    @endphp
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                        <td class="px-4 py-4 text-sm">
                            <div class="font-semibold text-gray-900 dark:text-gray-100">{{ $user->name }}</div>
                            <div class="text-gray-500 dark:text-gray-400">{{ $user->email }}</div>
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-700 dark:text-gray-200">
                            <select
                                class="w-full rounded-md border border-gray-300 bg-white px-2 py-1 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
                                wire:change="updateRole({{ $user->id }}, $event.target.value)"
                            >
                                @foreach ($this->roleOptions as $roleOption)
                                    <option value="{{ $roleOption }}" @selected($user->role === $roleOption)>
                                        {{ \Illuminate\Support\Str::headline($roleOption) }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-700 dark:text-gray-200">
                            {{ $subscription?->name ? \Illuminate\Support\Str::headline($subscription->name) : __('None') }}
                        </td>
                        <td class="px-4 py-4 text-sm">
                            @if ($subscription)
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $subscription->stripe_status === 'active' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-500/10 dark:text-emerald-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-500/10 dark:text-amber-300' }}">
                                    {{ \Illuminate\Support\Str::headline($subscription->stripe_status) }}
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                    {{ __('No subscription') }}
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-sm text-right">
                            <button
                                type="button"
                                wire:click="impersonate({{ $user->id }})"
                                class="inline-flex items-center rounded-md border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-600 transition hover:bg-slate-100 dark:border-slate-600 dark:text-slate-300 dark:hover:bg-slate-700"
                            >
                                {{ __('Impersonate') }}
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-300">
                            {{ __('No users match the current filters.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $this->users->links() }}
    </div>

    @error('role')
        <div class="rounded-md border border-rose-300 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-500/40 dark:bg-rose-500/10 dark:text-rose-200">
            {{ $message }}
        </div>
    @enderror
</div>
