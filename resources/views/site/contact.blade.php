@php($contact = config('backtoburkina.contact'))
<x-layouts.site title="Contact" description="Contactez Travel Express à Bobo-Dioulasso : une question sur les études en Chine, votre dossier, un recrutement ou un partenariat.">
    <x-site.page-hero eyebrow="Contact" title="Parlons de votre projet." lead="Une question sur les études en Chine, votre dossier, un recrutement ou un partenariat ? Écrivez-nous : un membre de l’équipe vous répond." />

    <section class="mx-auto grid max-w-[1200px] gap-8 px-4 py-14 sm:px-6 sm:py-20 lg:grid-cols-[1.25fr_0.75fr] lg:gap-10">
        {{-- Formulaire --}}
        <div data-reveal class="card p-5 sm:p-8">
            @if (session('sent'))
                <div class="flex flex-col items-center gap-4 py-10 text-center">
                    <span class="relative flex size-20 items-center justify-center">
                        <span class="absolute inset-0 animate-ping rounded-full bg-ok-500/20 [animation-iteration-count:2]"></span>
                        <span class="relative flex size-20 items-center justify-center rounded-full bg-linear-to-br from-ok-500 to-ok-700 text-white"><x-icon name="check" :size="36" stroke-width="2.6" /></span>
                    </span>
                    <h2 class="text-[26px] font-bold text-navy-950">Message envoyé, merci !</h2>
                    <p class="max-w-md text-[15.5px] leading-relaxed text-ink-600">Nous l’avons bien reçu. Un membre de l’équipe Travel Express vous répondra par e-mail{{ $contact['hours'] ? ', pendant nos horaires d’ouverture' : '' }}.</p>
                    <div class="mt-2 flex flex-col gap-2.5 min-[460px]:flex-row">
                        <a href="{{ route('home') }}" class="btn-secondary">Retour à l’accueil</a>
                        <a href="{{ route('welcome') }}" class="btn-primary">Créer mon compte</a>
                    </div>
                </div>
            @else
                <form method="POST" action="{{ route('contact.store') }}" class="flex flex-col gap-5" x-data="{ sending: false, length: {{ mb_strlen(old('message', '')) }} }" @submit="sending = true">
                    @csrf
                    <div class="flex flex-col gap-1">
                        <h2 class="text-[24px] font-bold text-navy-950">Écrivez-nous</h2>
                        <p class="text-sm text-ink-600">Les champs marqués d’un astérisque sont obligatoires.</p>
                    </div>

                    @if ($errors->any())
                        <div class="flex items-center gap-3 rounded-2xl bg-err-50 px-4 py-3.5 text-sm font-semibold text-err-700 ring-1 ring-err-700/15"><x-icon name="help" :size="18" /> Vérifiez les champs signalés ci-dessous.</div>
                    @endif

                    {{-- Piège à robots, invisible pour les personnes --}}
                    <div class="absolute -left-[9999px] h-0 w-0 overflow-hidden" aria-hidden="true">
                        <label>Site web <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                    </div>

                    <fieldset class="flex flex-col gap-2.5">
                        <legend class="field-label mb-2.5">Votre demande concerne *</legend>
                        <div class="grid gap-2 sm:grid-cols-2" x-data="{ subject: @js(old('subject', $subject ?? 'candidature')) }">
                            @foreach ($subjects as $key => $label)
                                <label class="flex cursor-pointer items-center gap-3 rounded-xl px-3.5 py-3 ring-1 transition" :class="subject === '{{ $key }}' ? 'bg-brand-50 ring-2 ring-brand-500' : 'bg-white ring-ink-200 hover:ring-brand-300'">
                                    <input type="radio" name="subject" value="{{ $key }}" x-model="subject" class="sr-only">
                                    <span class="flex size-5 shrink-0 items-center justify-center rounded-full ring-2 transition" :class="subject === '{{ $key }}' ? 'bg-brand-600 ring-brand-600' : 'ring-ink-300'"><span class="size-2 rounded-full bg-white" x-show="subject === '{{ $key }}'"></span></span>
                                    <span class="text-[14.5px] font-semibold text-navy-950">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                        <x-field-error name="subject" />
                    </fieldset>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <label class="flex flex-col gap-1.5 sm:col-span-2">
                            <span class="field-label">Nom et prénom *</span>
                            <input name="name" value="{{ old('name', auth()->user()?->name) }}" autocomplete="name" required maxlength="120" class="field">
                            <x-field-error name="name" />
                        </label>
                        <label class="flex flex-col gap-1.5">
                            <span class="field-label">Adresse e-mail *</span>
                            <input name="email" type="email" value="{{ old('email', auth()->user()?->email) }}" autocomplete="email" required maxlength="160" placeholder="vous@exemple.com" class="field">
                            <x-field-error name="email" />
                        </label>
                        <label class="flex flex-col gap-1.5">
                            <span class="field-label">Téléphone <span class="font-medium text-ink-500">(facultatif)</span></span>
                            <input name="phone" type="tel" value="{{ old('phone') }}" autocomplete="tel" maxlength="30" placeholder="+226 70 00 00 00" class="field">
                            <x-field-error name="phone" />
                        </label>
                    </div>

                    <label class="flex flex-col gap-1.5">
                        <span class="flex items-baseline justify-between"><span class="field-label">Votre message *</span><span class="text-xs text-ink-500"><span x-text="length"></span> / 5000</span></span>
                        <textarea name="message" rows="6" required minlength="10" maxlength="5000" @input="length = $el.value.length" placeholder="Votre projet, votre niveau d’études, vos questions…" class="field py-3 leading-relaxed">{{ old('message') }}</textarea>
                        <x-field-error name="message" />
                    </label>

                    <label class="flex cursor-pointer items-start gap-3">
                        <input type="checkbox" name="consent" value="1" @checked(old('consent')) required class="mt-0.5 size-5 shrink-0 rounded accent-brand-600">
                        <span class="text-[13.5px] leading-snug text-ink-700">J’accepte que Travel Express utilise ces informations pour répondre à ma demande. Voir la <a href="{{ route('legal.privacy') }}" class="font-semibold text-brand-600 hover:underline">politique de confidentialité</a>. *</span>
                    </label>
                    <x-field-error name="consent" />

                    <button class="btn-primary min-h-[54px] text-base sm:self-start sm:px-10" :disabled="sending">
                        <span x-show="!sending" class="flex items-center gap-2">Envoyer mon message <x-icon name="send" :size="18" /></span>
                        <span x-show="sending" x-cloak class="flex items-center gap-2"><span class="size-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span> Envoi…</span>
                    </button>
                </form>
            @endif
        </div>

        {{-- Coordonnées --}}
        <aside class="flex flex-col gap-4" data-reveal-stagger="100">
            <div data-reveal="right" class="bg-hero relative flex flex-col gap-5 overflow-hidden rounded-[24px] p-6 text-white sm:p-7">
                <div class="bg-weave pointer-events-none absolute inset-y-0 right-0 w-1/2 opacity-50 [mask-image:linear-gradient(to_left,black,transparent)]"></div>
                <span class="relative flex items-center gap-3"><x-logo :size="44" /><span class="flex flex-col leading-tight"><span class="font-display text-lg font-bold">Travel Express</span><span class="text-[13px] text-white/60">Agence de Bobo-Dioulasso</span></span></span>
                <ul class="relative flex flex-col gap-4 text-[14.5px]">
                    <li class="flex items-start gap-3"><span class="glass flex size-9 shrink-0 items-center justify-center rounded-xl text-gold-300"><x-icon name="map" :size="17" /></span><span class="flex flex-col"><span class="text-xs text-white/55">Adresse</span>{{ $contact['address'] }}</span></li>
                    @if ($contact['phone'])
                        <li class="flex items-start gap-3"><span class="glass flex size-9 shrink-0 items-center justify-center rounded-xl text-gold-300"><x-icon name="phone" :size="17" /></span><span class="flex flex-col"><span class="text-xs text-white/55">Téléphone</span><a href="tel:{{ str_replace(' ', '', $contact['phone']) }}" class="hover:text-gold-200">{{ $contact['phone'] }}</a></span></li>
                    @endif
                    <li class="flex items-start gap-3"><span class="glass flex size-9 shrink-0 items-center justify-center rounded-xl text-gold-300"><x-icon name="mail" :size="17" /></span><span class="flex min-w-0 flex-col"><span class="text-xs text-white/55">E-mail</span><a href="mailto:{{ $contact['email'] }}" class="truncate hover:text-gold-200">{{ $contact['email'] }}</a></span></li>
                    @if ($contact['hours'])
                        <li class="flex items-start gap-3"><span class="glass flex size-9 shrink-0 items-center justify-center rounded-xl text-gold-300"><x-icon name="clock" :size="17" /></span><span class="flex flex-col"><span class="text-xs text-white/55">Horaires</span>{{ $contact['hours'] }}</span></li>
                    @endif
                </ul>
                <div class="relative flex flex-col gap-2.5">
                    @if ($contact['whatsapp'])
                        <a href="https://wa.me/{{ $contact['whatsapp'] }}" target="_blank" rel="noopener" class="btn bg-[#25D366] text-white hover:brightness-95"><x-icon name="chat" :size="18" /> Écrire sur WhatsApp</a>
                    @endif
                    <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode('Travel Express '.$contact['address']) }}" target="_blank" rel="noopener" class="btn-outline-light"><x-icon name="map" :size="18" /> Voir sur la carte</a>
                </div>
            </div>

            <a data-reveal="right" href="{{ route('faq') }}" class="card card-hover group flex items-center gap-4 p-5">
                <span class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600"><x-icon name="help" :size="20" /></span>
                <span class="flex flex-1 flex-col"><span class="font-bold text-navy-950">Questions fréquentes</span><span class="text-[13px] text-ink-600">La réponse y est peut-être déjà.</span></span>
                <x-icon name="chevron" :size="18" class="text-ink-300 transition group-hover:translate-x-0.5 group-hover:text-brand-600" />
            </a>
            <a data-reveal="right" href="{{ route('login') }}" class="card card-hover group flex items-center gap-4 p-5">
                <span class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600"><x-icon name="folder" :size="20" /></span>
                <span class="flex flex-1 flex-col"><span class="font-bold text-navy-950">Vous avez déjà un dossier ?</span><span class="text-[13px] text-ink-600">Écrivez à votre conseiller depuis votre espace.</span></span>
                <x-icon name="chevron" :size="18" class="text-ink-300 transition group-hover:translate-x-0.5 group-hover:text-brand-600" />
            </a>
        </aside>
    </section>
</x-layouts.site>
