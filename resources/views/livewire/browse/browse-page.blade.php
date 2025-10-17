<div class="space-y-12">
    @if ($locked)
        <section class="relative overflow-hidden rounded-3xl border border-slate-800/70 bg-slate-950/80 p-10 text-center shadow-[0_0_60px_-30px_rgba(16,185,129,0.6)]">
            <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-emerald-500/10 via-slate-900/20 to-transparent"></div>
            <div class="relative mx-auto max-w-2xl space-y-5">
                <p class="text-xs font-semibold uppercase tracking-[0.4em] text-emerald-300">@lang('browse.locked.eyebrow')</p>
                <h2 class="text-3xl font-semibold text-white sm:text-4xl">@lang('browse.locked.hero_title')</h2>
                <p class="text-base text-slate-300 sm:text-lg">@lang('browse.locked.hero_body')</p>
                <div class="flex flex-col justify-center gap-3 pt-2 sm:flex-row">
                    <a href="{{ localized_route('login') }}" class="inline-flex items-center justify-center rounded-full bg-emerald-500 px-6 py-2 text-sm font-semibold text-emerald-950 shadow-lg shadow-emerald-500/25 transition hover:bg-emerald-400">
                        @lang('browse.locked.cta_primary')
                    </a>
                    <a href="{{ localized_route('register') }}" class="inline-flex items-center justify-center rounded-full border border-emerald-400/60 px-6 py-2 text-sm font-semibold text-emerald-200 transition hover:border-emerald-300 hover:text-emerald-100">
                        @lang('browse.locked.cta_secondary')
                    </a>
                    <a href="{{ localized_route('pricing') }}" class="inline-flex items-center justify-center rounded-full border border-slate-700 px-6 py-2 text-sm font-semibold text-slate-200 transition hover:border-emerald-300 hover:text-emerald-100">
                        @lang('browse.locked.cta_tertiary')
                    </a>
                </div>
            </div>
        </section>

        <div class="grid gap-10 lg:grid-cols-[minmax(0,2fr),minmax(0,1fr)]">
            <section class="space-y-8 rounded-3xl border border-slate-800 bg-slate-900/60 p-8">
                <header class="space-y-3">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-emerald-300">@lang('browse.locked.feature_title')</h3>
                    <p class="text-sm text-slate-300">@lang('subscriptions.errors.access_required')</p>
                </header>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="rounded-2xl border border-slate-800 bg-slate-950/60 p-5 text-left">
                        <p class="text-sm font-semibold text-slate-100">@lang('browse.locked.feature_filters')</p>
                    </div>
                    <div class="rounded-2xl border border-slate-800 bg-slate-950/60 p-5 text-left">
                        <p class="text-sm font-semibold text-slate-100">@lang('browse.locked.feature_watchlist')</p>
                    </div>
                    <div class="rounded-2xl border border-slate-800 bg-slate-950/60 p-5 text-left sm:col-span-2">
                        <p class="text-sm font-semibold text-slate-100">@lang('browse.locked.feature_briefings')</p>
                    </div>
                </div>

                <div class="rounded-2xl border border-emerald-500/40 bg-emerald-500/5 p-6 text-left">
                    <h4 class="text-sm font-semibold uppercase tracking-wide text-emerald-200">@lang('browse.locked.insights_title')</h4>
                    <p class="mt-3 text-sm text-slate-200">@lang('browse.locked.insights_body')</p>
                </div>
            </section>

            <aside class="space-y-8">
                <div class="rounded-3xl border border-slate-800 bg-slate-900/60 p-7">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-400">@lang('browse.locked.getting_started_title')</h3>
                    <ol class="mt-4 space-y-3 text-sm text-slate-200">
                        <li class="flex gap-3">
                            <span class="inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-500/20 text-xs font-semibold text-emerald-200">1</span>
                            <span>@lang('browse.locked.getting_started_step_one')</span>
                        </li>
                        <li class="flex gap-3">
                            <span class="inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-500/20 text-xs font-semibold text-emerald-200">2</span>
                            <span>@lang('browse.locked.getting_started_step_two')</span>
                        </li>
                        <li class="flex gap-3">
                            <span class="inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-500/20 text-xs font-semibold text-emerald-200">3</span>
                            <span>@lang('browse.locked.getting_started_step_three')</span>
                        </li>
                    </ol>
                </div>

                <div class="rounded-3xl border border-emerald-500/40 bg-emerald-500/5 p-7 text-left">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-emerald-200">@lang('browse.locked.faq_title')</h3>
                    <dl class="mt-4 space-y-4 text-sm text-slate-100">
                        <div>
                            <dt class="font-semibold text-emerald-200">@lang('browse.locked.faq_cancel_question')</dt>
                            <dd class="mt-1 text-slate-200">@lang('browse.locked.faq_cancel_answer')</dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-emerald-200">@lang('browse.locked.faq_regions_question')</dt>
                            <dd class="mt-1 text-slate-200">@lang('browse.locked.faq_regions_answer')</dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-emerald-200">@lang('browse.locked.faq_updates_question')</dt>
                            <dd class="mt-1 text-slate-200">@lang('browse.locked.faq_updates_answer')</dd>
                        </div>
                    </dl>
                </div>

                <div class="rounded-3xl border border-slate-800 bg-slate-900/60 p-7 text-center">
                    <p class="text-sm text-slate-300">@lang('browse.locked.cta_checkout_blurb')</p>
                    <a href="{{ localized_route('checkout') }}" class="mt-4 inline-flex items-center justify-center rounded-full bg-emerald-500 px-5 py-2 text-sm font-semibold text-emerald-950 transition hover:bg-emerald-400">
                        @lang('browse.locked.cta_checkout')
                    </a>
                </div>
            </aside>
        </div>
    @else
        <section class="rounded-3xl border border-slate-800/70 bg-slate-950/80 p-10">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
                <div class="max-w-2xl space-y-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.4em] text-emerald-300">@lang('browse.unlocked.eyebrow')</p>
                    <h2 class="text-3xl font-semibold text-white sm:text-4xl">@lang('browse.unlocked.hero_title')</h2>
                    <p class="text-base text-slate-300 sm:text-lg">@lang('browse.unlocked.hero_body')</p>
                </div>
                <div class="rounded-2xl border border-emerald-500/30 bg-emerald-500/5 p-6 text-sm text-emerald-100">
                    <p>@lang('browse.unlocked.trending_helper')</p>
                </div>
            </div>
        </section>

        <div class="grid gap-10 xl:grid-cols-[320px,1fr]">
            <aside class="space-y-6">
                <div class="rounded-3xl border border-slate-800 bg-slate-900/70 p-6">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-400">@lang('browse.unlocked.filter_title')</h3>
                    <p class="mt-3 text-sm text-slate-300">@lang('browse.unlocked.filter_body')</p>
                    <p class="mt-4 text-xs text-slate-500">@lang('browse.unlocked.filter_hint')</p>
                    <div class="mt-6">
                        @livewire('media-filters')
                    </div>
                </div>

                <div class="rounded-3xl border border-slate-800 bg-slate-900/70 p-6">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-400">@lang('browse.unlocked.collections_title')</h3>
                    <p class="mt-3 text-sm text-slate-300">@lang('browse.unlocked.collections_body')</p>
                    <ul class="mt-5 space-y-2 text-sm text-slate-200">
                        <li class="rounded-2xl border border-slate-800 bg-slate-950/70 px-4 py-3">@lang('browse.unlocked.collection_award')</li>
                        <li class="rounded-2xl border border-slate-800 bg-slate-950/70 px-4 py-3">@lang('browse.unlocked.collection_critics')</li>
                        <li class="rounded-2xl border border-slate-800 bg-slate-950/70 px-4 py-3">@lang('browse.unlocked.collection_coming')</li>
                    </ul>
                </div>
            </aside>

            <section class="space-y-10">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-2xl font-semibold text-slate-50">@lang('browse.unlocked.trending_title')</h3>
                        <p class="text-sm text-slate-400">@lang('browse.unlocked.trending_body')</p>
                    </div>
                    <div class="flex gap-3">
                        <button type="button" class="rounded-full border border-slate-700 px-4 py-2 text-sm font-medium text-slate-200 transition hover:border-emerald-400 hover:text-emerald-200">
                            @lang('browse.unlocked.trending_save')
                        </button>
                        <button type="button" class="rounded-full bg-emerald-500 px-4 py-2 text-sm font-semibold text-emerald-950 transition hover:bg-emerald-400">
                            @lang('browse.unlocked.trending_randomize')
                        </button>
                    </div>
                </div>

                @livewire('trending-reel')

                <div class="space-y-6">
                    <div>
                        <h3 class="text-xl font-semibold text-slate-50">@lang('browse.unlocked.playbooks_title')</h3>
                        <p class="mt-2 text-sm text-slate-400">@lang('browse.unlocked.playbooks_body')</p>
                    </div>
                    <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
                            <h4 class="text-base font-semibold text-slate-100">@lang('browse.unlocked.playbook_weekend')</h4>
                            <p class="mt-2 text-sm text-slate-400">@lang('browse.unlocked.playbook_weekend_body')</p>
                        </div>
                        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
                            <h4 class="text-base font-semibold text-slate-100">@lang('browse.unlocked.playbook_family')</h4>
                            <p class="mt-2 text-sm text-slate-400">@lang('browse.unlocked.playbook_family_body')</p>
                        </div>
                        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
                            <h4 class="text-base font-semibold text-slate-100">@lang('browse.unlocked.playbook_deepcut')</h4>
                            <p class="mt-2 text-sm text-slate-400">@lang('browse.unlocked.playbook_deepcut_body')</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div>
                        <h3 class="text-xl font-semibold text-slate-50">@lang('browse.unlocked.updates_title')</h3>
                        <p class="mt-2 text-sm text-slate-400">@lang('browse.unlocked.updates_body')</p>
                    </div>
                    <div class="grid gap-5 md:grid-cols-3">
                        <div class="rounded-2xl border border-emerald-500/30 bg-emerald-500/5 p-5">
                            <h4 class="text-base font-semibold text-emerald-100">@lang('browse.unlocked.update_filters_title')</h4>
                            <p class="mt-2 text-sm text-emerald-100/80">@lang('browse.unlocked.update_filters_body')</p>
                        </div>
                        <div class="rounded-2xl border border-emerald-500/30 bg-emerald-500/5 p-5">
                            <h4 class="text-base font-semibold text-emerald-100">@lang('browse.unlocked.update_lists_title')</h4>
                            <p class="mt-2 text-sm text-emerald-100/80">@lang('browse.unlocked.update_lists_body')</p>
                        </div>
                        <div class="rounded-2xl border border-emerald-500/30 bg-emerald-500/5 p-5">
                            <h4 class="text-base font-semibold text-emerald-100">@lang('browse.unlocked.update_notifications_title')</h4>
                            <p class="mt-2 text-sm text-emerald-100/80">@lang('browse.unlocked.update_notifications_body')</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-3xl border border-slate-800 bg-slate-900/70 p-6 text-center">
                    <h4 class="text-base font-semibold text-slate-100">@lang('browse.unlocked.empty_state_title')</h4>
                    <p class="mt-2 text-sm text-slate-400">@lang('browse.unlocked.empty_state_body')</p>
                </div>
            </section>
        </div>
    @endif
</div>
