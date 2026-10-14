@props(['icon', 'label', 'value', 'hint' => null, 'href' => null, 'tone' => 'brand'])
@php
$tones = [
    'brand' => 'bg-brand-50 text-brand-600',
    'gold' => 'bg-gold-50 text-gold-700',
    'ok' => 'bg-ok-50 text-ok-700',
    'warn' => 'bg-warn-50 text-warn-700',
    'ink' => 'bg-ink-50 text-ink-600',
];
$tag = $href ? 'a' : 'div';
@endphp
<{{ $tag }} @if ($href) href="{{ $href }}" @endif {{ $attributes->class(['card flex flex-col gap-3 p-4 sm:p-5', 'card-hover group' => $href]) }}>
    <span class="flex items-center justify-between gap-2">
        <span class="text-[12.5px] font-semibold text-ink-500">{{ $label }}</span>
        <span class="flex size-8 shrink-0 items-center justify-center rounded-lg {{ $tones[$tone] ?? $tones['brand'] }}"><x-icon :name="$icon" :size="16" /></span>
    </span>
    <span class="font-display text-[26px] font-extrabold leading-none text-navy-950 tabular-nums sm:text-[30px]">{{ $value }}</span>
    @if ($hint)
        <span class="flex items-center gap-1 text-[12.5px] text-ink-500">{{ $hint }} @if ($href)<x-icon name="chevron" :size="13" class="transition group-hover:translate-x-0.5" />@endif</span>
    @endif
</{{ $tag }}>
