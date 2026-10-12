@props(['size' => 40, 'invert' => false])
{{-- Monogramme BB, avec l'étoile du drapeau burkinabè en exposant --}}
<span {{ $attributes->class([
    'relative inline-flex shrink-0 items-center justify-center rounded-[30%] font-display font-extrabold tracking-[-0.04em]',
    'bg-white text-gold-600 shadow-lg shadow-navy-950/20' => $invert,
    'bg-linear-to-br from-gold-400 via-gold-500 to-gold-600 text-white shadow-[inset_0_1px_0_rgb(255_255_255/0.4),0_8px_18px_-8px_rgb(142_98_2/0.7)]' => ! $invert,
]) }} style="width: {{ $size }}px; height: {{ $size }}px; font-size: {{ round($size * 0.38) }}px">
    BB
    @if ($size >= 36)
        <svg viewBox="0 0 24 24" class="absolute right-[13%] top-[12%]" style="width: {{ round($size * 0.2) }}px; height: {{ round($size * 0.2) }}px" aria-hidden="true">
            <path d="M12 2.5l2.6 6.3 6.9.5-5.3 4.4 1.7 6.7L12 16.8l-5.9 3.6 1.7-6.7-5.3-4.4 6.9-.5z" fill="currentColor" opacity=".9"/>
        </svg>
    @endif
</span>
