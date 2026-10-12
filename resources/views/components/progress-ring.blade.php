@props(['percent' => 0, 'size' => 96, 'stroke' => 9, 'dark' => false])
@php
$r = ($size - $stroke) / 2;
$c = 2 * M_PI * $r;
$offset = $c * (1 - max(0, min(100, $percent)) / 100);
@endphp
<span {{ $attributes->class('relative inline-flex shrink-0 items-center justify-center') }} style="width: {{ $size }}px; height: {{ $size }}px" role="img" aria-label="{{ $percent }} % accompli">
    <svg width="{{ $size }}" height="{{ $size }}" viewBox="0 0 {{ $size }} {{ $size }}" class="-rotate-90">
        <defs>
            <linearGradient id="ring-{{ $size }}" x1="0" x2="1" y1="0" y2="1">
                <stop offset="0%" stop-color="{{ $dark ? '#F3D49E' : '#0A84FF' }}" />
                <stop offset="100%" stop-color="{{ $dark ? '#E0AE4C' : '#0071E3' }}" />
            </linearGradient>
        </defs>
        <circle cx="{{ $size / 2 }}" cy="{{ $size / 2 }}" r="{{ $r }}" fill="none" stroke-width="{{ $stroke }}" class="{{ $dark ? 'stroke-white/15' : 'stroke-ink-100' }}" />
        <circle cx="{{ $size / 2 }}" cy="{{ $size / 2 }}" r="{{ $r }}" fill="none" stroke-width="{{ $stroke }}" stroke-linecap="round"
                stroke="url(#ring-{{ $size }})" stroke-dasharray="{{ $c }}" stroke-dashoffset="{{ $offset }}" class="transition-[stroke-dashoffset] duration-700" />
    </svg>
    <span class="absolute inset-0 flex flex-col items-center justify-center">
        <span @class(['font-display font-extrabold leading-none', 'text-white' => $dark, 'text-navy-950' => ! $dark]) style="font-size: {{ round($size * 0.26) }}px">{{ $percent }}<span class="text-[0.55em]">%</span></span>
    </span>
</span>
