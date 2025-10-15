<div class="mx-auto w-full max-w-6xl px-6 py-12">
    <div class="flex flex-col gap-12 lg:flex-row lg:items-start">
        <aside class="w-full space-y-6 lg:w-1/3">
            <div class="overflow-hidden rounded-3xl bg-slate-900/60 ring-1 ring-slate-800/60">
                <img
                    alt="{{ __('Profile photo of :name', ['name' => $person->name]) }}"
                    class="h-full w-full object-cover"
                    src="{{ $person->profileImageUrl() }}"
                />
            </div>

            <div class="rounded-3xl bg-slate-900/60 p-6 ring-1 ring-slate-800/60">
                <h2 class="text-lg font-semibold tracking-tight text-white">{{ __('Personal Details') }}</h2>

                <dl class="mt-4 space-y-4 text-sm text-slate-300">
                    @forelse($personalDetails as $label => $value)
                        <div class="border-b border-slate-800/70 pb-3 last:border-0 last:pb-0">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $label }}</dt>
                            <dd class="mt-1 text-slate-200">
                                @if($label === __('Homepage'))
                                    <a class="text-sky-400 hover:text-sky-300" href="{{ $value }}" rel="noopener" target="_blank">
                                        {{ $value }}
                                    </a>
                                @else
                                    {{ $value }}
                                @endif
                            </dd>
                        </div>
                    @empty
                        <p class="text-slate-500">{{ __('No personal details available.') }}</p>
                    @endforelse
                </dl>
            </div>
        </aside>

        <section class="flex-1 space-y-12">
            <header class="space-y-4">
                <h1 class="text-4xl font-semibold tracking-tight text-white">{{ $person->name }}</h1>

                @if(filled($person->biography))
                    <p class="text-base leading-relaxed text-slate-300">{{ $person->biography }}</p>
                @endif
            </header>

            @if(! empty($biographyTranslations))
                <section class="space-y-6">
                    <h2 class="text-2xl font-semibold text-white">{{ __('Biography Translations') }}</h2>

                    <div class="grid gap-6 md:grid-cols-2">
                        @foreach($biographyTranslations as $translation)
                            <article class="rounded-3xl bg-slate-900/60 p-6 ring-1 ring-slate-800/60">
                                <h3 class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $translation['label'] }}</h3>
                                <p class="mt-3 whitespace-pre-line text-sm leading-relaxed text-slate-300">{{ $translation['text'] }}</p>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif

            @if($movieCreditsByRole->isNotEmpty())
                <section class="space-y-4">
                    <h2 class="text-2xl font-semibold text-white">{{ __('Movie Credits') }}</h2>

                    @foreach($movieCreditsByRole as $role => $credits)
                        <div class="rounded-3xl bg-slate-900/60 p-6 ring-1 ring-slate-800/60">
                            <h3 class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                {{ \Illuminate\Support\Str::headline($role ?: __('Other')) }}
                            </h3>

                            <ul class="mt-4 divide-y divide-slate-800/70">
                                @foreach($credits as $credit)
                                    <li class="flex flex-col justify-between gap-2 py-3 sm:flex-row sm:items-center">
                                        <span class="font-medium text-slate-100">{{ $credit->title ?? $credit->name }}</span>

                                        <div class="text-sm text-slate-400">
                                            @if($credit->pivot?->character)
                                                <span>{{ __('as') }} {{ $credit->pivot->character }}</span>
                                            @elseif($credit->pivot?->job)
                                                <span>{{ $credit->pivot->job }}</span>
                                            @endif

                                            @php
                                                $year = $credit->release_date?->format('Y') ?? $credit->first_air_date?->format('Y');
                                            @endphp

                                            @if($year)
                                                <span class="ml-2 text-slate-500">({{ $year }})</span>
                                            @endif
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </section>
            @endif

            @if($tvCreditsByRole->isNotEmpty())
                <section class="space-y-4">
                    <h2 class="text-2xl font-semibold text-white">{{ __('TV Credits') }}</h2>

                    @foreach($tvCreditsByRole as $role => $credits)
                        <div class="rounded-3xl bg-slate-900/60 p-6 ring-1 ring-slate-800/60">
                            <h3 class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                {{ \Illuminate\Support\Str::headline($role ?: __('Other')) }}
                            </h3>

                            <ul class="mt-4 divide-y divide-slate-800/70">
                                @foreach($credits as $credit)
                                    <li class="flex flex-col justify-between gap-2 py-3 sm:flex-row sm:items-center">
                                        <span class="font-medium text-slate-100">{{ $credit->name }}</span>

                                        <div class="text-sm text-slate-400">
                                            @if($credit->pivot?->character)
                                                <span>{{ __('as') }} {{ $credit->pivot->character }}</span>
                                            @elseif($credit->pivot?->job)
                                                <span>{{ $credit->pivot->job }}</span>
                                            @endif

                                            @php
                                                $year = $credit->first_air_date?->format('Y');
                                            @endphp

                                            @if($year)
                                                <span class="ml-2 text-slate-500">({{ $year }})</span>
                                            @endif
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </section>
            @endif
        </section>
    </div>
</div>
