<div class="mx-auto max-w-4xl space-y-10">
    @if (session('error'))
        <div class="rounded-2xl border border-rose-500/40 bg-rose-500/10 px-5 py-3 text-sm text-rose-100">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid gap-6 md:grid-cols-{{ max(count($plans), 1) }}">
        @forelse ($plans as $billingInterval => $priceId)
            <div wire:key="checkout-plan-{{ $billingInterval }}" class="flex flex-col justify-between rounded-3xl border border-slate-800 bg-slate-900/70 p-6 shadow-lg shadow-emerald-500/5">
                <div class="space-y-3">
                    <h2 class="text-xl font-semibold text-white">
                        {{ __("Premium :interval", ['interval' => ucfirst($billingInterval)]) }}
                    </h2>
                    <p class="text-sm text-slate-300">
                        {{ __('Full detail pages, watch history sync, and access to parser insights every week.') }}
                    </p>
                </div>

                <button
                    type="button"
                    wire:click="selectPlan('{{ $priceId }}')"
                    class="mt-6 w-full rounded-full bg-emerald-500 px-4 py-2 text-sm font-semibold text-emerald-950 transition hover:bg-emerald-400"
                >
                    {{ __('Start :interval plan', ['interval' => $billingInterval]) }}
                </button>
            </div>
        @empty
            <div class="rounded-3xl border border-amber-500/40 bg-amber-500/10 px-6 py-8 text-center text-sm text-amber-100">
                {{ __('Stripe prices are not configured yet. Add STRIPE_MONTHLY_PRICE or STRIPE_YEARLY_PRICE to your environment.') }}
            </div>
        @endforelse
    </div>

    <div class="rounded-3xl border border-slate-800/60 bg-slate-950/60 p-6 text-sm text-slate-300">
        <h3 class="text-base font-semibold text-slate-100">{{ __('Why upgrade?') }}</h3>
        <ul class="mt-3 list-disc space-y-2 pl-6">
            <li>{{ __('Deep metadata: credits, streaming providers, and localization without leaving the page.') }}</li>
            <li>{{ __('Personalized dashboards with watch history, genre affinity, and release radar alerts.') }}</li>
            <li>{{ __('Support the ingestion pipeline that keeps the catalog fresh and globally localized.') }}</li>
        </ul>
    </div>

    <form id="checkout-subscription-form" method="POST" action="{{ route('subscriptions.store', ['locale' => app()->getLocale()]) }}" class="hidden">
        @csrf
        <input type="hidden" name="price" value="">
    </form>
</div>

@push('scripts')
    <script>
        if (! window.__checkoutPlanListener) {
            window.__checkoutPlanListener = (event) => {
                const form = document.getElementById('checkout-subscription-form');

                if (! form) {
                    return;
                }

                const priceInput = form.querySelector('input[name="price"]');
                const price = event?.detail?.price ?? '';

                if (! priceInput || price === '') {
                    return;
                }

                priceInput.value = price;
                form.submit();
            };

            window.addEventListener('subscriptions.store', window.__checkoutPlanListener);
        }
    </script>
@endpush
