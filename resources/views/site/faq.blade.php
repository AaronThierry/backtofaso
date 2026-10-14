<x-layouts.site title="Questions fréquentes" description="Destinations, langue, bourses, documents, visa, vie sur place, vivier de talents, compte : les réponses aux questions les plus posées à Travel Express.">
    <x-site.page-hero eyebrow="FAQ" crumb="Questions fréquentes" title="Questions fréquentes" lead="Les réponses aux questions qu’on nous pose le plus souvent. Vous ne trouvez pas la vôtre ? Écrivez-nous, un conseiller vous répond." />

    <section class="mx-auto grid max-w-[1200px] gap-10 px-4 py-14 sm:px-6 sm:py-20 lg:grid-cols-[260px_1fr] lg:gap-14"
             x-data="{ q: '', open: null, match(text) { return !this.q.trim() || text.toLowerCase().includes(this.q.trim().toLowerCase()); } }">
        {{-- Recherche et thèmes --}}
        <aside class="flex flex-col gap-5 lg:sticky lg:top-24 lg:self-start">
            <label class="field-group min-h-[52px]">
                <x-icon name="search" class="ml-4 text-ink-400" />
                <span class="sr-only">Rechercher une question</span>
                <input x-model="q" type="search" placeholder="Rechercher…" class="min-w-0 flex-1 bg-transparent px-3 text-[15px] outline-none placeholder:text-ink-400">
            </label>
            <nav class="hidden flex-col gap-1 lg:flex" aria-label="Thèmes">
                @foreach ($faq as $theme => $group)
                    <a href="#{{ \Illuminate\Support\Str::slug($theme) }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-[15px] font-semibold text-ink-700 transition hover:bg-ink-50 hover:text-navy-950">
                        <span class="flex size-8 items-center justify-center rounded-lg bg-brand-50 text-brand-600"><x-icon :name="$group['icon']" :size="16" /></span>
                        {{ $theme }}
                        <span class="ml-auto text-xs text-ink-400">{{ count($group['items']) }}</span>
                    </a>
                @endforeach
            </nav>
            <div class="hidden flex-col gap-3 rounded-2xl bg-canvas p-5 lg:flex">
                <span class="font-display text-[17px] font-bold text-navy-950">Pas trouvé ?</span>
                <span class="text-sm leading-relaxed text-ink-600">Posez votre question, nous vous répondons rapidement.</span>
                <a href="{{ route('contact') }}" class="btn-primary min-h-11 text-sm">Nous écrire</a>
            </div>
        </aside>

        {{-- Questions --}}
        <div class="flex flex-col gap-12">
            @foreach ($faq as $theme => $group)
                @php($texts = collect($group['items'])->map(fn ($it) => mb_strtolower($it[0].' '.$it[1]))->join(' '))
                <section id="{{ \Illuminate\Support\Str::slug($theme) }}" class="flex scroll-mt-24 flex-col gap-4" x-show="match(@js($texts))">
                    <h2 data-reveal class="flex items-center gap-3 text-[24px] font-bold text-navy-950">
                        <span class="flex size-10 items-center justify-center rounded-xl bg-linear-to-br from-brand-500 to-brand-600 text-white shadow-glow"><x-icon :name="$group['icon']" :size="19" /></span>
                        {{ $theme }}
                    </h2>
                    <div class="flex flex-col gap-3">
                        @foreach ($group['items'] as $i => [$question, $answer])
                            @php($key = \Illuminate\Support\Str::slug($theme).'-'.$i)
                            <div data-reveal class="card overflow-hidden transition duration-300" x-show="match(@js($question.' '.$answer))" :class="open === '{{ $key }}' && 'ring-brand-200 shadow-lift'">
                                <button type="button" @click="open = open === '{{ $key }}' ? null : '{{ $key }}'" :aria-expanded="open === '{{ $key }}'"
                                        class="flex min-h-[64px] w-full items-center justify-between gap-4 px-5 py-4 text-left font-display text-[16.5px] font-bold text-navy-950 sm:px-6">
                                    {{ $question }}
                                    <span class="flex size-9 shrink-0 items-center justify-center rounded-full transition duration-300" :class="open === '{{ $key }}' ? 'rotate-45 bg-brand-600 text-white' : 'bg-ink-50 text-ink-700'"><x-icon name="plus" :size="18" stroke-width="2.2" /></span>
                                </button>
                                <div class="grid transition-[grid-template-rows] duration-400 ease-[cubic-bezier(0.2,0.7,0.2,1)]" :class="open === '{{ $key }}' ? 'grid-rows-[1fr]' : 'grid-rows-[0fr]'">
                                    <div class="overflow-hidden">
                                        <p class="px-5 pb-6 text-[15.5px] leading-relaxed text-ink-600 sm:px-6">{{ $answer }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endforeach

            <div x-show="q.trim() && !@js(collect($faq)->flatMap(fn ($g) => $g['items'])->map(fn ($it) => mb_strtolower($it[0].' '.$it[1]))->values()).some(t => t.includes(q.trim().toLowerCase()))" x-cloak>
                <x-empty icon="search" title="Aucune réponse trouvée" text="Essayez un autre mot, ou posez-nous directement votre question.">
                    <a href="{{ route('contact') }}" class="btn-primary min-h-11 text-sm">Poser ma question</a>
                </x-empty>
            </div>
        </div>
    </section>

    <x-site.cta />
</x-layouts.site>
