@props(['user' => null, 'initials' => null, 'size' => 44, 'tone' => 'brand', 'square' => false])
@php
$tones = [
    'brand' => 'bg-linear-to-br from-brand-100 to-brand-200 text-brand-800',
    'gold' => 'bg-linear-to-br from-gold-100 to-gold-200 text-gold-700',
    'ink' => 'bg-linear-to-br from-ink-100 to-ink-200 text-ink-800',
    'green' => 'bg-linear-to-br from-ok-50 to-[#CDEFD9] text-ok-700',
    'navy' => 'bg-linear-to-br from-navy-700 to-navy-900 text-white',
];
$label = $initials ?? $user?->initials;
@endphp
@if ($user?->avatar_url)
    <img src="{{ $user->avatar_url }}" alt="" {{ $attributes->class(['shrink-0 object-cover ring-1 ring-ink-900/5', $square ? 'rounded-[30%]' : 'rounded-full']) }} style="width: {{ $size }}px; height: {{ $size }}px">
@else
    <span {{ $attributes->class(['inline-flex shrink-0 items-center justify-center font-bold tracking-tight ring-1 ring-ink-900/5', $square ? 'rounded-[30%]' : 'rounded-full', $tones[$tone] ?? $tones['brand']]) }}
          style="width: {{ $size }}px; height: {{ $size }}px; font-size: {{ round($size * 0.34) }}px">{{ $label }}</span>
@endif
