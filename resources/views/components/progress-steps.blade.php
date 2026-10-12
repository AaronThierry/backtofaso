@props(['current', 'total' => 4, 'labels' => null])
<div {{ $attributes->class('flex flex-col gap-2') }}>
    @unless ($labels)
        <span class="flex items-baseline justify-between text-[12.5px] font-semibold text-ink-600">
            <span>Étape <b class="text-ink-900">{{ $current }}</b> sur {{ $total }}</span>
            <span class="text-brand-600">{{ round($current / $total * 100) }} %</span>
        </span>
    @endunless
    <ol class="grid gap-1.5" style="grid-template-columns: repeat({{ $total }}, minmax(0, 1fr))">
        @for ($i = 1; $i <= $total; $i++)
            <li class="flex flex-col gap-2">
                <span @class(['h-1.5 rounded-full transition-colors', 'bg-linear-to-r from-brand-500 to-brand-600' => $i <= $current, 'bg-ink-100' => $i > $current])></span>
                @if ($labels)
                    <span class="text-[13px] {{ $i === $current ? 'font-bold text-ink-900' : 'font-semibold text-ink-600' }}">{{ $i }} · {{ $labels[$i - 1] }}</span>
                @endif
            </li>
        @endfor
    </ol>
</div>
