@props(['current', 'total' => 4, 'labels' => null])
<div {{ $attributes->class('flex flex-col gap-1.5') }}>
    @unless ($labels)
        <span class="text-[12.5px] font-semibold text-ink-600">Étape {{ $current }} sur {{ $total }}</span>
    @endunless
    <ol class="grid gap-1" style="grid-template-columns: repeat({{ $total }}, minmax(0, 1fr))">
        @for ($i = 1; $i <= $total; $i++)
            <li class="flex flex-col gap-2">
                <span class="h-1 rounded-sm {{ $i <= $current ? 'bg-brand-600' : 'bg-ink-100' }}"></span>
                @if ($labels)
                    <span class="text-[13px] {{ $i === $current ? 'font-bold text-ink-900' : 'font-semibold text-ink-600' }}">{{ $i }} · {{ $labels[$i - 1] }}</span>
                @endif
            </li>
        @endfor
    </ol>
</div>
