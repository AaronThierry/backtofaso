@props(['eyebrow' => null, 'title', 'lead' => null, 'crumb' => null])
{{-- Bandeau d'en-tête des pages du site public. Slot par défaut : boutons ; slot « aside » : visuel à droite. --}}
<section class="bg-hero relative overflow-hidden text-white" data-parallax-scope>
    <div class="bg-weave pointer-events-none absolute inset-y-0 right-0 w-1/2 opacity-50 [mask-image:linear-gradient(to_left,black,transparent)]"></div>
    <div class="pointer-events-none absolute -left-32 top-0 size-[420px] animate-drift rounded-full bg-brand-500/25 blur-3xl"></div>

    <div @class(['relative mx-auto grid max-w-[1200px] items-center gap-10 px-4 pb-16 pt-8 sm:px-6 sm:pb-20 sm:pt-12 lg:pb-24 lg:pt-16', 'lg:grid-cols-[1.2fr_1fr]' => isset($aside)])>
        <div class="flex flex-col gap-5">
            <nav data-reveal="fade" aria-label="Fil d’Ariane" class="flex items-center gap-2 text-[13px] text-white/55">
                <a href="{{ route('home') }}" class="transition hover:text-white">Accueil</a>
                <x-icon name="chevron" :size="14" />
                <span class="text-white/85">{{ $crumb ?? $title }}</span>
            </nav>
            @if ($eyebrow)
                <span data-reveal="fade" class="eyebrow text-gold-300">{{ $eyebrow }}</span>
            @endif
            <h1 data-lines class="max-w-3xl text-[clamp(2.2rem,7vw,4rem)] font-extrabold leading-[1.02]"><span class="block">{{ $title }}</span></h1>
            @if ($lead)
                <p data-reveal style="--reveal-delay: 200ms" class="max-w-2xl text-[16.5px] leading-relaxed text-white/70 sm:text-lg">{{ $lead }}</p>
            @endif
            @if (trim($slot))
                <div data-reveal style="--reveal-delay: 300ms" class="flex flex-col gap-3 pt-1 min-[460px]:flex-row min-[460px]:flex-wrap">{{ $slot }}</div>
            @endif
        </div>
        @isset($aside)
            <div data-reveal="zoom" style="--reveal-delay: 200ms" class="relative mx-auto w-full max-w-[440px] lg:mx-0 lg:justify-self-end">{{ $aside }}</div>
        @endisset
    </div>
</section>
