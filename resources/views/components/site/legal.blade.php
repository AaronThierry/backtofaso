@props(['title', 'description' => null, 'updated' => null, 'toc' => []])
{{-- Mise en page des pages légales. $toc : [ancre => titre]. Le contenu utilise <section id="…"> avec <h2>. --}}
<x-layouts.site :title="$title" :description="$description">
    <x-site.page-hero eyebrow="Informations légales" :title="$title" :lead="$updated ? 'Dernière mise à jour : '.$updated : null" />

    <div class="mx-auto grid max-w-[1200px] gap-10 px-4 py-14 sm:px-6 sm:py-20 lg:grid-cols-[240px_1fr] lg:gap-14">
        @if ($toc)
            <nav class="hidden flex-col gap-1 lg:sticky lg:top-24 lg:flex lg:self-start" aria-label="Sommaire">
                <span class="px-3 pb-2 text-xs font-bold uppercase tracking-[0.14em] text-ink-500">Sommaire</span>
                @foreach ($toc as $anchor => $label)
                    <a href="#{{ $anchor }}" class="rounded-lg px-3 py-2 text-[14px] font-semibold text-ink-700 transition hover:bg-ink-50 hover:text-navy-950">{{ $label }}</a>
                @endforeach
                <span class="mx-3 my-3 h-px bg-ink-100"></span>
                @foreach (['legal.notice' => 'Mentions légales', 'legal.privacy' => 'Confidentialité', 'legal.terms' => 'Conditions d’utilisation'] as $route => $label)
                    @unless (request()->routeIs($route))
                        <a href="{{ route($route) }}" class="rounded-lg px-3 py-2 text-[13.5px] text-brand-600 transition hover:bg-brand-50">{{ $label }}</a>
                    @endunless
                @endforeach
            </nav>
        @endif

        <article class="legal flex max-w-3xl flex-col gap-10 text-[15.5px] leading-relaxed text-ink-700
                        [&_a]:font-semibold [&_a]:text-brand-600 [&_a:hover]:underline
                        [&_h2]:mb-3 [&_h2]:text-[22px] [&_h2]:font-bold [&_h2]:text-navy-950
                        [&_li]:pl-1 [&_p+p]:mt-3 [&_strong]:text-ink-900
                        [&_section]:scroll-mt-24 [&_ul]:mt-3 [&_ul]:flex [&_ul]:list-disc [&_ul]:flex-col [&_ul]:gap-1.5 [&_ul]:pl-5">
            {{ $slot }}
        </article>
    </div>
</x-layouts.site>
