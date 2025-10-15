@extends('layouts.app', [
    'title' => 'Start your free trial',
    'header' => 'Create your Stream Scout account',
    'subheader' => 'Join the community to save favourites, sync watchlists, and unlock premium browse tools.',
])

@section('content')
    <div class="mx-auto max-w-3xl space-y-10">
        <section class="rounded-3xl border border-slate-800 bg-slate-900/60 p-10 shadow-xl shadow-emerald-500/5">
            <h2 class="text-2xl font-semibold text-slate-100">Why join?</h2>
            <ul class="mt-6 space-y-4 text-slate-300">
                <li class="flex items-start gap-3">
                    <span class="mt-1 inline-flex h-6 w-6 items-center justify-center rounded-full bg-emerald-500/20 text-sm font-semibold text-emerald-300">1</span>
                    <div>
                        <p class="font-semibold text-slate-100">Personalised curation</p>
                        <p class="text-sm text-slate-400">Follow hand-picked collections, save filters, and receive alerts when new releases match your taste.</p>
                    </div>
                </li>
                <li class="flex items-start gap-3">
                    <span class="mt-1 inline-flex h-6 w-6 items-center justify-center rounded-full bg-emerald-500/20 text-sm font-semibold text-emerald-300">2</span>
                    <div>
                        <p class="font-semibold text-slate-100">Stream availability radar</p>
                        <p class="text-sm text-slate-400">Track where to watch across services with regional availability updates refreshed every hour.</p>
                    </div>
                </li>
                <li class="flex items-start gap-3">
                    <span class="mt-1 inline-flex h-6 w-6 items-center justify-center rounded-full bg-emerald-500/20 text-sm font-semibold text-emerald-300">3</span>
                    <div>
                        <p class="font-semibold text-slate-100">Cancel anytime</p>
                        <p class="text-sm text-slate-400">Enjoy a generous trial window before billing starts. You can cancel directly from your account dashboard.</p>
                    </div>
                </li>
            </ul>
        </section>

        <section class="rounded-3xl border border-slate-800 bg-slate-950/70 p-10">
            <h2 class="text-xl font-semibold text-slate-100">Ready to dive in?</h2>
            <p class="mt-4 text-sm text-slate-400">Create your account in moments, then choose the plan that fits best. Existing members can jump straight to checkout.</p>
            <div class="mt-6 flex flex-col gap-4 sm:flex-row">
                <a href="{{ route('pricing') }}" class="inline-flex items-center justify-center rounded-full bg-emerald-500 px-6 py-3 text-sm font-semibold text-emerald-950 transition hover:bg-emerald-400">Compare plans</a>
                <a href="{{ route('subscriptions.checkout') }}" class="inline-flex items-center justify-center rounded-full border border-emerald-500/40 px-6 py-3 text-sm font-semibold text-emerald-200 transition hover:border-emerald-400/80 hover:text-emerald-100">Already a member? Continue to checkout</a>
            </div>
        </section>
    </div>
@endsection
