@props(['title' => 'Prêt à commencer ?', 'text' => 'Créez votre compte en quelques minutes : un conseiller Travel Express prend ensuite contact avec vous.'])
{{-- Bloc d'appel final des pages du site public. Slot : boutons (par défaut, inscription + contact). --}}
<section class="mx-auto max-w-[1200px] px-4 py-16 sm:px-6 sm:py-24">
    <div data-reveal="zoom" class="relative overflow-hidden rounded-[28px] bg-linear-to-br from-brand-500 to-brand-700 px-6 py-12 text-white shadow-lift sm:rounded-[36px] sm:px-12 sm:py-14 lg:px-16">
        <div class="bg-weave pointer-events-none absolute inset-0 opacity-40"></div>
        <div class="pointer-events-none absolute -right-20 -top-20 size-72 animate-drift rounded-full bg-white/15 blur-2xl"></div>
        <div class="relative flex flex-wrap items-center justify-between gap-8">
            <div class="flex max-w-xl flex-col gap-3">
                <h2 class="text-[clamp(1.9rem,5vw,2.8rem)] font-extrabold leading-[1.05]">{{ $title }}</h2>
                <p class="text-[16px] leading-relaxed text-white/80">{{ $text }}</p>
            </div>
            <div class="flex w-full flex-col gap-3 min-[460px]:w-auto min-[460px]:flex-row">
                @if (trim($slot))
                    {{ $slot }}
                @else
                    <a href="{{ route('welcome') }}" class="btn-gold shine">Créer mon compte <x-icon name="arrow" :size="18" /></a>
                    <a href="{{ route('contact') }}" class="btn-outline-light">Nous contacter</a>
                @endif
            </div>
        </div>
    </div>
</section>
