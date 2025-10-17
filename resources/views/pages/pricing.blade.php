<section class="grid gap-8 lg:grid-cols-3">
    @foreach ($plans as $plan)
        <div class="{{ $plan['card_classes'] }}">
            <p class="text-sm uppercase tracking-widest text-emerald-400">{{ $plan['name'] }}</p>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-4xl font-bold text-slate-50">{{ $plan['price'] }}</span>
                <span class="text-sm text-slate-400">{{ $plan['frequency'] }}</span>
            </div>
            <p class="mt-4 text-sm text-slate-300">{{ $plan['description'] }}</p>
            <a href="#" class="{{ $plan['cta']['classes'] }}">{{ $plan['cta']['label'] }}</a>
            <ul class="mt-6 space-y-3 text-sm text-slate-200">
                @foreach ($plan['features'] as $feature)
                    <li class="flex items-start gap-3">
                        <svg class="mt-0.5 h-4 w-4 text-emerald-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path fill-rule="evenodd" d="M2.25 12a9.75 9.75 0 1 1 19.5 0 9.75 9.75 0 0 1-19.5 0Zm13.1-3.53a.75.75 0 0 0-1.2-.9L11 11.234 9.6 9.985a.75.75 0 1 0-1 1.12l2 1.75a.75.75 0 0 0 1.07-.08Z" clip-rule="evenodd" />
                        </svg>
                        {{ $feature }}
                    </li>
                @endforeach
            </ul>
        </div>
    @endforeach
</section>
