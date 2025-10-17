@extends('layouts.app', [
    'title' => __('Workspace agreements'),
    'header' => __('Workspace agreements'),
    'subheader' => __('Review the distribution rules and privacy terms required to collaborate inside OMDb Stream.'),
])

@section('content')
    <div class="mx-auto max-w-4xl">
        <div class="rounded-3xl border border-emerald-500/15 bg-slate-950/80 p-10 shadow-[0_30px_60px_-36px_rgba(16,185,129,0.6)] backdrop-blur">
            <div class="space-y-6">
                <p class="text-sm text-slate-300">{{ __('We bring studio-grade guardrails to ensure every partner distributes metadata, artwork, and viewing links responsibly. Please confirm the agreements below before creating a workspace or inviting teammates.') }}</p>

                <div class="grid gap-6 md:grid-cols-2">
                    <article class="rounded-2xl border border-slate-800/70 bg-slate-950/60 p-5">
                        <h2 class="text-base font-semibold text-slate-100">{{ __('Content distribution terms') }}</h2>
                        <p class="mt-2 text-xs text-slate-300">{{ __('Outlines how trailers, stills, and early review screeners can be shared across internal and partner platforms.') }}</p>
                        <ul class="mt-4 space-y-2 text-xs text-slate-400">
                            <li>{{ __('• Protect embargoed material until release windows open.') }}</li>
                            <li>{{ __('• Route press inquiries through authorized studio contacts.') }}</li>
                            <li>{{ __('• Use approved artwork packs when syndicating metadata.') }}</li>
                        </ul>
                    </article>

                    <article class="rounded-2xl border border-slate-800/70 bg-slate-950/60 p-5">
                        <h2 class="text-base font-semibold text-slate-100">{{ __('Data privacy & governance') }}</h2>
                        <p class="mt-2 text-xs text-slate-300">{{ __('Explains how viewer data, sentiment dashboards, and user accounts must be handled across your organization.') }}</p>
                        <ul class="mt-4 space-y-2 text-xs text-slate-400">
                            <li>{{ __('• Respect viewer opt-outs and localize consent notices.') }}</li>
                            <li>{{ __('• Grant least-privilege access for contractors and agencies.') }}</li>
                            <li>{{ __('• Notify OMDb Stream support within 24 hours of suspected incidents.') }}</li>
                        </ul>
                    </article>
                </div>

                <article class="rounded-2xl border border-slate-800/70 bg-slate-950/60 p-5">
                    <h2 class="text-base font-semibold text-slate-100">{{ __('Community standards & usage') }}</h2>
                    <p class="mt-2 text-xs text-slate-300">{{ __('Defines acceptable behavior when collaborating in shared watchlists, notes, and editorial planning boards.') }}</p>
                    <ul class="mt-4 space-y-2 text-xs text-slate-400">
                        <li>{{ __('• Keep feedback constructive and free of discriminatory language.') }}</li>
                        <li>{{ __('• Avoid uploading materials that you do not have rights to distribute.') }}</li>
                        <li>{{ __('• Report abusive activity with timestamps so moderators can review quickly.') }}</li>
                    </ul>
                </article>
            </div>

            <form action="{{ localized_route('register') }}" method="GET" class="mt-10 space-y-5" data-agreements-form="" target="_blank">
                <fieldset class="space-y-4">
                    <legend class="text-sm font-semibold text-slate-200">{{ __('Confirm your agreements') }}</legend>

                    <label class="flex items-start gap-3 text-sm text-slate-200">
                        <input type="checkbox" name="agreements[]" value="distribution" class="mt-1.5 h-4 w-4 flex-shrink-0 rounded border-slate-700 bg-slate-900 text-emerald-400 focus:ring-emerald-500" required>
                        <span>{{ __('I agree to follow the content distribution terms including embargo windows and approved assets.') }}</span>
                    </label>

                    <label class="flex items-start gap-3 text-sm text-slate-200">
                        <input type="checkbox" name="agreements[]" value="privacy" class="mt-1.5 h-4 w-4 flex-shrink-0 rounded border-slate-700 bg-slate-900 text-emerald-400 focus:ring-emerald-500" required>
                        <span>{{ __('I agree to the data privacy & governance rules outlined in the OMDb Stream Privacy Policy.') }}</span>
                    </label>

                    <label class="flex items-start gap-3 text-sm text-slate-200">
                        <input type="checkbox" name="agreements[]" value="community" class="mt-1.5 h-4 w-4 flex-shrink-0 rounded border-slate-700 bg-slate-900 text-emerald-400 focus:ring-emerald-500" required>
                        <span>{{ __('I agree to uphold community standards when collaborating with teammates and partners.') }}</span>
                    </label>
                </fieldset>

                <p class="text-xs text-slate-400">{{ __('Selecting continue confirms your acceptance and will open the sign-up form in a new tab.') }}</p>

                <button type="submit" class="w-full rounded-full bg-emerald-500 px-4 py-2 text-sm font-semibold text-emerald-950 shadow-[0_12px_32px_-14px_rgba(16,185,129,0.82)] transition hover:bg-emerald-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-300">
                    {{ __('Continue to create your account') }}
                </button>
            </form>
        </div>
    </div>
@endsection
