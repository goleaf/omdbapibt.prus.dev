<div class="space-y-6">
    @if (session()->has('status'))
        <div class="rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('status') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    <div class="rounded-lg bg-white p-6 shadow">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Subscription</h2>
                <p class="text-sm text-gray-500">Manage your current plan, billing status, and trial information.</p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a
                    class="inline-flex items-center justify-center rounded-md border border-blue-200 bg-blue-50 px-4 py-2 text-sm font-medium text-blue-700 transition hover:border-blue-300 hover:bg-blue-100"
                    href="{{ localized_route('billing.portal') }}"
                >
                    Open billing portal
                </a>

                @if ($subscription)
                    @if ($isOnGracePeriod)
                        <button
                            wire:click="resumeSubscription"
                            type="button"
                            class="inline-flex items-center justify-center rounded-md border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm font-medium text-emerald-700 transition hover:border-emerald-300 hover:bg-emerald-100"
                        >
                            Resume subscription
                        </button>
                    @elseif (! $isCancelled)
                        <button
                            wire:click="cancelSubscription"
                            type="button"
                            class="inline-flex items-center justify-center rounded-md border border-red-200 bg-red-50 px-4 py-2 text-sm font-medium text-red-700 transition hover:border-red-300 hover:bg-red-100"
                        >
                            Cancel subscription
                        </button>
                    @endif
                @endif
            </div>
        </div>

        <div class="mt-6 border-t border-gray-200 pt-6">
            @if ($subscription)
                <dl class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Plan</dt>
                        <dd class="mt-1 text-base text-gray-900">{{ $planDisplay }}</dd>
                        @if ($subscription->stripe_price)
                            <p class="text-xs text-gray-500">Price ID: {{ $subscription->stripe_price }}</p>
                        @endif
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1 text-base text-gray-900">{{ $statusLabel }}</dd>
                        @if ($isOnGracePeriod && $gracePeriodEnds)
                            <p class="text-xs text-gray-500">Access ends {{ $gracePeriodEnds }}</p>
                        @endif
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Trial</dt>
                        <dd class="mt-1 text-base text-gray-900">{{ $trialLabel }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Quantity</dt>
                        <dd class="mt-1 text-base text-gray-900">{{ $quantity }}</dd>
                    </div>
                </dl>

                <div class="mt-6 rounded-lg border border-gray-200 bg-gray-50 p-4">
                    <h3 class="text-sm font-semibold text-gray-800">Upcoming invoice</h3>

                    @if ($invoiceDetails)
                        <p class="mt-2 text-sm text-gray-700">
                            Next charge of
                            <span class="font-semibold">{{ $invoiceDetails['amount'] }} {{ $invoiceDetails['currency'] }}</span>
                            {{ $invoiceDetails['charge_date'] ? 'on '.$invoiceDetails['charge_date'] : '' }}.
                        </p>

                        @if ($invoiceDetails['period_range'])
                            <p class="text-xs text-gray-500">Billing period: {{ $invoiceDetails['period_range'] }}</p>
                        @endif
                    @else
                        <p class="mt-2 text-sm text-gray-600">No upcoming invoices are scheduled right now.</p>
                    @endif
                </div>

                <div class="mt-6">
                    <h3 class="text-sm font-semibold text-gray-800">Payment history</h3>

                    @if ($payments->isEmpty())
                        <p class="mt-2 text-sm text-gray-600">No payments have been recorded yet for this subscription.</p>
                    @else
                        <div class="mt-3 overflow-hidden rounded-lg border border-gray-200">
                            <div class="max-h-72 overflow-y-auto">
                                <table class="min-w-full divide-y divide-gray-200 text-sm">
                                    <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500">
                                        <tr>
                                            <th scope="col" class="px-4 py-3 text-left">Invoice</th>
                                            <th scope="col" class="px-4 py-3 text-left">Status</th>
                                            <th scope="col" class="px-4 py-3 text-left">Amount</th>
                                            <th scope="col" class="px-4 py-3 text-left">Paid at</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 bg-white">
                                        @foreach ($payments as $payment)
                                            <tr class="hover:bg-gray-50/80">
                                                <td class="px-4 py-3">
                                                    <div class="font-medium text-gray-900">{{ $payment->invoice_number ?? $payment->invoice_id }}</div>
                                                    @if ($payment->invoice_number && $payment->invoice_id)
                                                        <div class="text-xs text-gray-500">{{ $payment->invoice_id }}</div>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 text-gray-700">
                                                    <span class="inline-flex items-center rounded-full border border-gray-200 bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-700">
                                                        {{ $payment->statusLabel() }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 font-medium text-gray-900">{{ $payment->formattedAmount() }}</td>
                                                <td class="px-4 py-3 text-gray-700">
                                                    @php
                                                        $paidAt = $payment->paid_at?->setTimezone(config('app.timezone'));
                                                    @endphp

                                                    {{ $paidAt ? $paidAt->toDayDateTimeString() : '—' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            @else
                <p class="text-sm text-gray-600">
                    You do not currently have an active subscription. Start a plan to unlock premium features and see billing details here.
                </p>
            @endif
        </div>
    </div>
</div>
