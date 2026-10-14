@props(['user', 'ratio' => 'aspect-[4/5]'])
{{-- Grande photo de profil d'un talent (cartes du vivier). Sans photo : initiales sur fond bleu nuit. Le slot se superpose à l'image. --}}
<div {{ $attributes->class(['relative overflow-hidden bg-navy-900', $ratio]) }}>
    @if ($user->avatar_url)
        <img src="{{ $user->avatar_url }}" alt="Photo de {{ $user->name }}" loading="lazy"
             class="absolute inset-0 size-full object-cover object-center transition duration-700 ease-out group-hover:scale-105">
    @else
        <div class="bg-hero absolute inset-0 flex items-center justify-center">
            <div class="bg-weave absolute inset-0 opacity-50"></div>
            <span class="relative font-display text-[clamp(3rem,9vw,4.5rem)] font-extrabold tracking-tight text-white/90 transition duration-700 group-hover:scale-110">{{ $user->initials }}</span>
        </div>
    @endif
    <div class="pointer-events-none absolute inset-x-0 bottom-0 h-1/2 bg-linear-to-t from-navy-950/75 via-navy-950/25 to-transparent"></div>
    {{ $slot }}
</div>
