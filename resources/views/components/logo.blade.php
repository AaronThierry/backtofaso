@props(['size' => 40, 'invert' => false])
<span {{ $attributes->class(['inline-flex shrink-0 items-center justify-center rounded-xl font-bold tracking-tight', $invert ? 'bg-white text-gold-600' : 'bg-gold-600 text-white']) }}
      style="width: {{ $size }}px; height: {{ $size }}px; font-size: {{ round($size * 0.36) }}px">BB</span>
