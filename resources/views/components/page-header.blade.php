@props(['title', 'subtitle' => null, 'eyebrow' => null, 'back' => null])
{{-- En-tête commun des pages de l'espace membre. Slot nommé « actions » pour les boutons à droite. --}}
<header {{ $attributes->class('flex flex-wrap items-center justify-between gap-x-4 gap-y-3') }}>
    <div class="flex min-w-0 items-center gap-3">
        @if ($back)
            <a href="{{ $back }}" aria-label="Retour" class="icon-btn lg:hidden"><x-icon name="back" /></a>
        @endif
        <div class="flex min-w-0 flex-col gap-1">
            @if ($eyebrow)
                <span class="eyebrow">{{ $eyebrow }}</span>
            @endif
            <h1 class="text-[26px] font-bold leading-tight text-navy-950 lg:text-[34px]">{{ $title }}</h1>
            @if ($subtitle)
                <p class="text-[14px] leading-relaxed text-ink-600 lg:text-[15.5px]">{{ $subtitle }}</p>
            @endif
        </div>
    </div>
    @isset($actions)
        <div class="flex items-center gap-2.5">{{ $actions }}</div>
    @endisset
</header>
