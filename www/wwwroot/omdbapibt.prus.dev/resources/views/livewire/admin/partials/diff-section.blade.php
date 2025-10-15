@props(['title', 'items', 'type'])

@php
    $formatter = function ($value) {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if ($value === null) {
            return 'null';
        }

        if (is_scalar($value)) {
            return (string) $value;
        }

        return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    };
@endphp

<div class="space-y-3">
    <div class="flex items-center justify-between">
        <h4 class="text-sm font-semibold text-slate-700">{{ $title }}</h4>
        <span class="text-xs font-semibold text-slate-400">{{ count($items) }}</span>
    </div>

    @if (count($items) === 0)
        <p class="text-sm text-slate-400">No fields {{ $type }}.</p>
    @else
        <dl class="space-y-3">
            @foreach ($items as $key => $value)
                <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                    <dt class="text-xs font-semibold uppercase tracking-widest text-slate-500">{{ $key }}</dt>
                    @if ($type === 'updated')
                        <dd class="mt-2 text-sm text-rose-600">
                            From: <span class="font-mono">{{ $formatter($value['from'] ?? null) }}</span>
                        </dd>
                        <dd class="mt-1 text-sm text-emerald-600">
                            To: <span class="font-mono">{{ $formatter($value['to'] ?? null) }}</span>
                        </dd>
                    @else
                        <dd class="mt-2 text-sm text-slate-700 font-mono">{{ $formatter($value) }}</dd>
                    @endif
                </div>
            @endforeach
        </dl>
    @endif
</div>
