<section class="space-y-8">
    <div class="rounded-3xl border border-slate-800 bg-slate-900/70 p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm uppercase tracking-widest text-emerald-400">Subscription</p>
                <h2 class="text-2xl font-semibold text-slate-50">{{ $this->subscription['plan'] }}</h2>
                <p class="text-sm text-slate-400">Renews on {{ $this->subscription['renewal_date'] }} • {{ $this->subscription['price'] }}</p>
            </div>
            <div class="flex gap-3">
                <button class="rounded-full border border-slate-700 px-5 py-2 text-sm font-medium text-slate-200 transition hover:border-emerald-400 hover:text-emerald-200">Manage billing</button>
                <button class="rounded-full bg-emerald-500 px-5 py-2 text-sm font-semibold text-emerald-950 transition hover:bg-emerald-400">Upgrade plan</button>
            </div>
        </div>

        <ul class="mt-6 grid gap-3 sm:grid-cols-2">
            @foreach ($this->subscription['benefits'] as $benefit)
                <li class="flex items-start gap-3 rounded-2xl border border-slate-800 bg-slate-950/80 p-4 text-sm text-slate-200">
                    <svg class="mt-0.5 h-4 w-4 text-emerald-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path fill-rule="evenodd" d="M2.25 12a9.75 9.75 0 1 1 19.5 0 9.75 9.75 0 0 1-19.5 0Zm13.1-3.53a.75.75 0 0 0-1.2-.9L11 11.234 9.6 9.985a.75.75 0 1 0-1 1.12l2 1.75a.75.75 0 0 0 1.07-.08Z" clip-rule="evenodd" />
                    </svg>
                    {{ $benefit }}
                </li>
            @endforeach
        </ul>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="space-y-4 rounded-3xl border border-slate-800 bg-slate-900/70 p-6">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-400">Profile preferences</h3>
            <dl class="space-y-3">
                @foreach ($this->preferences as $preference)
                    <div class="flex items-center justify-between rounded-2xl border border-slate-800 bg-slate-950/80 px-4 py-3 text-sm text-slate-200">
                        <dt class="text-slate-400">{{ $preference['label'] }}</dt>
                        <dd class="font-medium text-slate-100">{{ $preference['value'] }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>

        <div class="space-y-4 rounded-3xl border border-slate-800 bg-slate-900/70 p-6">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-400">Security</h3>
            <ul class="space-y-3 text-sm text-slate-200">
                <li class="rounded-2xl border border-slate-800 bg-slate-950/80 px-4 py-3">
                    <div class="flex items-center justify-between">
                        <span>Password</span>
                        <button class="text-emerald-300 transition hover:text-emerald-200">Update</button>
                    </div>
                </li>
                <li class="rounded-2xl border border-slate-800 bg-slate-950/80 px-4 py-3">
                    <div class="flex items-center justify-between">
                        <span>Two-factor authentication</span>
                        <button class="text-emerald-300 transition hover:text-emerald-200">Manage</button>
                    </div>
                </li>
                <li class="rounded-2xl border border-slate-800 bg-slate-950/80 px-4 py-3">
                    <div class="flex items-center justify-between">
                        <span>Connected devices</span>
                        <button class="text-emerald-300 transition hover:text-emerald-200">Review</button>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</section>
