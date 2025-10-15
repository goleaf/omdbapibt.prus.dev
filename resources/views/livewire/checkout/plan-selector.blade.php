<div
    x-data="{
        submit(priceId) {
            this.$refs.price.value = priceId;

            if (typeof this.$refs.form.requestSubmit === 'function') {
                this.$refs.form.requestSubmit();

                return;
            }

            this.$refs.form.submit();
        }
    }"
    x-on:subscriptions\\.store.window="submit($event.detail.priceId)"
    class="mx-auto max-w-4xl space-y-10">
    <form x-ref="form" method="POST" action="{{ route('subscriptions.store') }}" class="hidden">
        @csrf
        <input type="hidden" name="price" x-ref="price">
    </form>

    @if (session('error'))
        <div class="rounded-2xl border border-rose-500/40 bg-rose-500/10 px-5 py-3 text-sm text-rose-100">
            {{ session('error') }}
        </div>
    @endif

    @error('plan')
        <div class="rounded-2xl border border-amber-500/40 bg-amber-500/10 px-5 py-3 text-sm text-amber-100">
            {{ $message }}
        </div>
    @enderror

    <div class="grid gap-6 md:grid-cols-{{ max(count($plans), 1) }}">
        @forelse ($plans as $planKey => $plan)
            @php
                $planName = $plan['name'] ?? \Illuminate\Support\Str::headline((string) $planKey);
                $interval = $plan['interval'] ?? 'month';

                if ($interval instanceof \UnitEnum) {
                    $interval = method_exists($interval, 'value') ? $interval->value : $interval->name;
                }

                $interval = \Illuminate\Support\Str::of((string) $interval)->lower();
                $intervalCount = (int) ($plan['interval_count'] ?? 1);
                $intervalLabel = $intervalCount > 1
                    ? $intervalCount . ' ' . \Illuminate\Support\Str::plural($interval, $intervalCount)
                    : $interval;
                $currency = \Illuminate\Support\Str::upper((string) ($plan['currency'] ?? 'USD'));
                $amount = number_format(((int) ($plan['amount'] ?? 0)) / 100, 2);
            @endphp

            <div class="flex flex-col justify-between rounded-3xl border border-slate-800 bg-slate-900/70 p-6 shadow-lg shadow-emerald-500/5">
                <div class="space-y-3">
                    <h2 class="text-xl font-semibold text-white">
                        {{ $planName }}
                    </h2>

                    <p class="text-sm text-slate-300">
                        {{ __('Full detail pages, watch history sync, and access to parser insights every week.') }}
                    </p>

                    <p class="text-2xl font-semibold text-emerald-400">
                        {{ $currency }} {{ $amount }}
                        <span class="text-sm font-normal text-slate-300">/{{ $intervalLabel }}</span>
                    </p>

                    @if (! empty($plan['features']))
                        <ul class="mt-4 space-y-2 text-sm text-slate-200">
                            @foreach ($plan['features'] as $feature)
                                <li class="flex items-start gap-2">
                                    <span aria-hidden="true" class="mt-1 inline-flex h-2 w-2 flex-shrink-0 rounded-full bg-emerald-400"></span>
                                    <span>{{ $feature }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <div class="mt-6">
                    <button
                        type="button"
                        wire:click="startCheckout('{{ $planKey }}')"
                        class="w-full rounded-full bg-emerald-500 px-4 py-2 text-sm font-semibold text-emerald-950 transition hover:bg-emerald-400">
                        {{ __('Start :plan plan', ['plan' => $planName]) }}
                    </button>
                </div>
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
</div>
