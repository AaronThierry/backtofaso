@props(['size' => 40, 'invert' => false])
@php($maskId = 'bf-'.\Illuminate\Support\Str::random(8))
{{-- Symbole Back to Faso : le B aux couleurs du drapeau, l'étoile et la flèche du retour, sur une tuile blanche --}}
<span {{ $attributes->class([
    'relative inline-flex shrink-0 items-center justify-center overflow-hidden rounded-[26%] bg-white',
    'shadow-lg shadow-navy-950/20' => $invert,
    'shadow-[0_8px_18px_-8px_rgb(10_20_40/0.45)] ring-1 ring-black/5' => ! $invert,
]) }} style="width: {{ $size }}px; height: {{ $size }}px">
    <svg viewBox="0 0 64 64" class="size-full" role="img" aria-label="Back to Faso">
        <g transform="translate(9.6 9.6) scale(0.7)">
            <defs>
                <mask id="{{ $maskId }}" maskUnits="userSpaceOnUse" x="-10" y="-10" width="84" height="84">
                    <rect x="-10" y="-10" width="84" height="84" fill="#fff"/>
                    <path d="M31 19l2.94 8.45 8.95.19-7.13 5.41 2.59 8.56L31 36.5l-7.35 5.11 2.59-8.56-7.13-5.41 8.95-.19z" fill="#000" stroke="#000" stroke-width="3.4" stroke-linejoin="round"/>
                </mask>
            </defs>
            <g transform="translate(0 -2)">
                <g mask="url(#{{ $maskId }})" fill="none" stroke-linejoin="round" stroke-width="7.6">
                    <path d="M18 12 H32 A9 9 0 0 1 32 30 H35 A11 11 0 0 1 35 52 H26 M31 47 L26 52 L31 57" stroke="#009E49" stroke-linecap="round"/>
                    <path d="M18 12 V52" stroke="#009E49" stroke-linecap="round"/>
                    <path d="M18 12 V31" stroke="#EF2B2D"/>
                    <path d="M18 12 H32 A9 9 0 0 1 32 30" stroke="#EF2B2D"/>
                    <circle cx="18" cy="12" r="3.8" fill="#EF2B2D"/>
                </g>
                <path d="M31 19l2.94 8.45 8.95.19-7.13 5.41 2.59 8.56L31 36.5l-7.35 5.11 2.59-8.56-7.13-5.41 8.95-.19z" fill="#FCD116"/>
            </g>
        </g>
    </svg>
</span>
