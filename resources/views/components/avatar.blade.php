@props(['user' => null, 'initials' => null, 'size' => 44, 'tone' => 'brand', 'square' => false])
@php
$tones = ['brand' => 'bg-brand-100 text-brand-700', 'gold' => 'bg-gold-100 text-gold-700', 'ink' => 'bg-ink-100 text-ink-700', 'green' => 'bg-ok-50 text-ok-700'];
$label = $initials ?? $user?->initials;
@endphp
@if ($user?->avatar_url)
    <img src="{{ $user->avatar_url }}" alt="" {{ $attributes->class(['shrink-0 object-cover', $square ? 'rounded-[15px]' : 'rounded-full']) }} style="width: {{ $size }}px; height: {{ $size }}px">
@else
    <span {{ $attributes->class(['inline-flex shrink-0 items-center justify-center font-bold', $square ? 'rounded-[15px]' : 'rounded-full', $tones[$tone] ?? $tones['brand']]) }}
          style="width: {{ $size }}px; height: {{ $size }}px; font-size: {{ round($size * 0.32) }}px">{{ $label }}</span>
@endif
