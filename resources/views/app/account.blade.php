@php
    $profile = $user->talentProfile;
    $completion = $profile?->completion();
    $wantsDestination = in_array($user->role, [\App\Models\User::ROLE_CANDIDATE, \App\Models\User::ROLE_STUDENT_ABROAD, \App\Models\User::ROLE_GRADUATE], true);
    $sections = array_filter([
        'informations' => ['user', 'Informations'],
        'talent' => $profile ? ['briefcase', 'Profil talent'] : null,
        'notifications' => ['bell', 'Notifications'],
        'securite' => ['shield', 'Sécurité'],
        'suppression' => $user->isStaff() ? null : ['trash', 'Supprimer le compte'],
    ]);
@endphp
<x-layouts.app title="Mon compte" active="account">
    <div class="flex flex-col gap-6 lg:gap-8">
        <x-page-header title="Mon compte" subtitle="Vos informations, votre sécurité et vos préférences." />
        <x-flash />
        @if ($errors->any())
            <div class="flex items-center gap-3 rounded-2xl bg-err-50 px-4 py-3.5 text-sm font-semibold text-err-700 ring-1 ring-err-700/15"><x-icon name="help" :size="18" /> Vérifiez les champs signalés ci-dessous.</div>
        @endif

        <div class="grid gap-6 lg:grid-cols-[300px_minmax(0,1fr)] lg:items-start lg:gap-8">
            {{-- Identité et rubriques --}}
            <aside class="flex flex-col gap-4 lg:sticky lg:top-10">
                <div class="card overflow-hidden">
                    <div class="bg-hero relative h-20"><div class="bg-weave absolute inset-0 opacity-60"></div></div>
                    <div class="relative -mt-12 flex flex-col items-center gap-3 px-5 pb-5 text-center">
                        {{-- Photo : clic pour changer --}}
                        <form method="POST" action="{{ route('account.photo') }}" enctype="multipart/form-data" x-data="{ sending: false }" class="relative">
                            @csrf
                            <label class="group relative block cursor-pointer rounded-full" title="Changer la photo">
                                <x-avatar :user="$user" :size="96" tone="gold" class="ring-4 ring-white shadow-lg" />
                                <span class="absolute inset-0 flex items-center justify-center rounded-full bg-navy-950/55 text-white opacity-0 transition group-hover:opacity-100" :class="sending && 'opacity-100'">
                                    <x-icon name="camera" :size="24" x-show="!sending" />
                                    <span x-show="sending" x-cloak class="size-6 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                                </span>
                                <span class="absolute bottom-0.5 right-0.5 flex size-8 items-center justify-center rounded-full bg-brand-600 text-white shadow-md ring-[3px] ring-white"><x-icon name="camera" :size="15" /></span>
                                <input type="file" name="avatar" accept="image/*" class="sr-only" @change="sending = true; $el.form.submit()">
                            </label>
                        </form>
                        @error('avatar')<span class="text-xs font-semibold text-err-700">{{ $message }}</span>@enderror
                        <div class="flex flex-col items-center gap-1.5">
                            <span class="font-display text-[20px] font-bold leading-tight text-navy-950">{{ $user->name }}</span>
                            <span class="inline-flex items-center rounded-full bg-brand-50 px-3 py-1 text-xs font-bold text-brand-700 ring-1 ring-brand-100">{{ $user->role_label }}</span>
                        </div>
                        @if ($user->avatar_path)
                            <form method="POST" action="{{ route('account.photo.delete') }}">@csrf @method('DELETE')
                                <button class="text-xs font-semibold text-ink-500 underline-offset-2 hover:text-err-700 hover:underline">Retirer la photo</button>
                            </form>
                        @endif
                    </div>
                    <nav class="hidden flex-col border-t border-ink-100 p-2 lg:flex" aria-label="Rubriques du compte">
                        @foreach ($sections as $id => [$icon, $label])
                            <a href="#{{ $id }}" @class(['flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition', 'text-err-700 hover:bg-err-50' => $id === 'suppression', 'text-ink-700 hover:bg-ink-50 hover:text-navy-950' => $id !== 'suppression'])>
                                <x-icon :name="$icon" :size="17" /> {{ $label }}
                                @if ($id === 'talent')<span class="ml-auto text-xs font-bold text-brand-600">{{ $completion['percent'] }} %</span>@endif
                            </a>
                        @endforeach
                    </nav>
                </div>
                <form method="POST" action="{{ route('logout') }}">@csrf
                    <button class="btn-secondary w-full text-err-700 hover:bg-err-50 hover:ring-err-700/20"><x-icon name="logout" /> Se déconnecter</button>
                </form>
            </aside>

            <div class="flex flex-col gap-6">
                {{-- Informations personnelles --}}
                <section id="informations" class="card scroll-mt-6">
                    <header class="flex items-center gap-3 border-b border-ink-100 px-5 py-4 sm:px-6">
                        <span class="flex size-9 items-center justify-center rounded-lg bg-brand-50 text-brand-600"><x-icon name="user" :size="18" /></span>
                        <span class="flex flex-col"><span class="text-[16px] font-bold text-navy-950">Informations personnelles</span><span class="text-[12.5px] text-ink-500">Visibles par votre conseiller, pas par les autres membres.</span></span>
                    </header>
                    <form method="POST" action="{{ route('account.profile') }}" class="flex flex-col gap-4 p-5 sm:p-6" x-data="{ saving: false }" @submit="saving = true">
                        @csrf @method('PUT')
                        <div class="grid gap-4 sm:grid-cols-2">
                            <label class="flex flex-col gap-1.5"><span class="field-label">Prénom</span>
                                <input name="first_name" value="{{ old('first_name', $user->first_name) }}" autocomplete="given-name" required class="field"><x-field-error name="first_name" /></label>
                            <label class="flex flex-col gap-1.5"><span class="field-label">Nom</span>
                                <input name="last_name" value="{{ old('last_name', $user->last_name) }}" autocomplete="family-name" required class="field"><x-field-error name="last_name" /></label>
                            <label class="flex flex-col gap-1.5"><span class="field-label">Téléphone</span>
                                <span class="field-group">
                                    <span class="flex items-center self-stretch border-r border-ink-200 bg-ink-50 px-3 text-[15px] font-semibold text-ink-800">+226</span>
                                    <input name="phone" value="{{ old('phone', preg_replace('/^\+226/', '', $user->phone)) }}" inputmode="tel" autocomplete="tel-national" required class="min-w-0 flex-1 bg-transparent px-3 text-[15px] outline-none">
                                </span>
                                <x-field-error name="phone" /><x-field-error name="phone_full" /></label>
                            <label class="flex flex-col gap-1.5"><span class="field-label">Adresse e-mail</span>
                                <span class="field-group bg-ink-50 hover:border-ink-200">
                                    <x-icon name="lock" :size="16" class="ml-3.5 text-ink-400" />
                                    <input value="{{ $user->email }}" disabled class="min-w-0 flex-1 bg-transparent px-3 text-[15px] text-ink-600 outline-none">
                                </span>
                                <span class="text-[12px] text-ink-500">Elle sert à vous connecter. Pour la changer, <a href="{{ route('contact', ['sujet' => 'autre']) }}" class="font-semibold text-brand-600 hover:underline">contactez-nous</a>.</span></label>
                            <label class="flex flex-col gap-1.5"><span class="field-label">Ville</span>
                                <select name="city" class="field">
                                    <option value="">Non précisée</option>
                                    @foreach ($cities as $city)
                                        <option @selected(old('city', $user->city) === $city)>{{ $city }}</option>
                                    @endforeach
                                </select></label>
                            <label class="flex flex-col gap-1.5"><span class="field-label">Date de naissance</span>
                                <input type="date" name="birth_date" value="{{ old('birth_date', $user->birth_date?->format('Y-m-d')) }}" class="field"><x-field-error name="birth_date" /></label>
                            @if ($wantsDestination)
                                <label class="flex flex-col gap-1.5"><span class="field-label">{{ $user->role === \App\Models\User::ROLE_CANDIDATE ? 'Pays visé' : 'Pays des études' }}</span>
                                    <select name="destination" class="field">
                                        <option value="">Je ne sais pas encore</option>
                                        @foreach ($destinations as $key => $name)
                                            <option value="{{ $key }}" @selected(old('destination', $user->destination) === $key)>{{ $name }}</option>
                                        @endforeach
                                    </select></label>
                                <label class="flex flex-col gap-1.5"><span class="field-label">Niveau dans la langue d’enseignement</span>
                                    <select name="language_level" class="field">
                                        <option value="">Non précisé</option>
                                        @foreach ($levels as $key => $label)
                                            <option value="{{ $key }}" @selected(old('language_level', $user->language_level) === $key)>{{ $label }}</option>
                                        @endforeach
                                    </select></label>
                            @endif
                        </div>
                        <div class="flex justify-end border-t border-ink-100 pt-4">
                            <button class="btn-primary min-h-11 px-6 text-sm" :disabled="saving">
                                <span x-show="!saving">Enregistrer</span>
                                <span x-show="saving" x-cloak class="flex items-center gap-2"><span class="size-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span> Enregistrement…</span>
                            </button>
                        </div>
                    </form>
                </section>

                {{-- Profil talent --}}
                @if ($profile)
                    <section id="talent" class="card scroll-mt-6">
                        <header class="flex items-center gap-3 border-b border-ink-100 px-5 py-4 sm:px-6">
                            <span class="flex size-9 items-center justify-center rounded-lg bg-brand-50 text-brand-600"><x-icon name="briefcase" :size="18" /></span>
                            <span class="flex flex-1 flex-col"><span class="text-[16px] font-bold text-navy-950">Profil talent</span><span class="text-[12.5px] text-ink-500">{{ $profile->verified_at ? 'Vérifié par Travel Express' : 'En attente de vérification par Travel Express' }}{{ $profile->is_public ? ' · visible des recruteurs' : ' · masqué aux recruteurs' }}</span></span>
                            <span class="text-sm font-bold text-navy-950">{{ $completion['percent'] }} %</span>
                        </header>
                        <div class="flex flex-col gap-4 p-5 sm:p-6">
                            <span class="block h-2 overflow-hidden rounded-full bg-ink-100"><span class="block h-full rounded-full bg-linear-to-r from-brand-500 to-gold-400" style="width: {{ $completion['percent'] }}%"></span></span>
                            <span class="flex flex-wrap gap-2">
                                @foreach ($completion['items'] as $item)
                                    <span @class(['inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold', 'bg-ok-50 text-ok-700' => $item['done'], 'bg-ink-50 text-ink-600 ring-1 ring-ink-200' => ! $item['done']])>
                                        <x-icon :name="$item['done'] ? 'check' : 'plus'" :size="12" stroke-width="2.6" /> {{ $item['label'] }}
                                    </span>
                                @endforeach
                            </span>
                            <div class="flex flex-wrap gap-2.5 border-t border-ink-100 pt-4">
                                <a href="{{ route('talents.edit') }}" class="btn-primary min-h-11 px-4 text-sm">{{ $completion['percent'] < 100 ? 'Compléter mon profil' : 'Modifier mon profil' }}</a>
                                <a href="{{ route('talents.show', $profile) }}" class="btn-secondary min-h-11 px-4 text-sm"><x-icon name="eye" :size="17" /> Voir comme un recruteur</a>
                            </div>
                        </div>
                    </section>
                @endif

                {{-- Notifications --}}
                <section id="notifications" class="card scroll-mt-6">
                    <header class="flex items-center gap-3 border-b border-ink-100 px-5 py-4 sm:px-6">
                        <span class="flex size-9 items-center justify-center rounded-lg bg-brand-50 text-brand-600"><x-icon name="bell" :size="18" /></span>
                        <span class="flex flex-col"><span class="text-[16px] font-bold text-navy-950">Notifications</span><span class="text-[12.5px] text-ink-500">Comment vous tenir informé de votre dossier et de vos messages.</span></span>
                    </header>
                    <form method="POST" action="{{ route('account.notifications') }}" x-data="{ dirty: false }" @change="dirty = true">
                        @csrf @method('PUT')
                        <div class="divide-y divide-ink-100">
                            @foreach ([
                                ['notify_push', 'bell', 'Notifications', 'Sur votre téléphone, via l’application'],
                                ['notify_sms', 'phone', 'SMS', 'Étapes importantes uniquement'],
                                ['notify_whatsapp', 'chat', 'WhatsApp', 'Messages de votre conseiller'],
                                ['notify_email', 'mail', 'E-mail', 'Récapitulatifs et documents'],
                            ] as [$name, $icon, $label, $sub])
                                <label class="flex cursor-pointer items-center gap-4 px-5 py-3.5 transition hover:bg-ink-50/60 sm:px-6" x-data="{ on: {{ $user->$name ? 'true' : 'false' }} }">
                                    <span class="flex size-9 shrink-0 items-center justify-center rounded-xl transition" :class="on ? 'bg-brand-50 text-brand-600' : 'bg-ink-50 text-ink-400'"><x-icon :name="$icon" :size="17" /></span>
                                    <span class="flex flex-1 flex-col"><span class="text-[14.5px] font-semibold text-navy-950">{{ $label }}</span><span class="text-[12.5px] text-ink-500">{{ $sub }}</span></span>
                                    <input type="checkbox" name="{{ $name }}" value="1" x-model="on" class="peer sr-only">
                                    <span class="flex h-[28px] w-[48px] shrink-0 rounded-full p-[3px] transition-colors peer-focus-visible:ring-4 peer-focus-visible:ring-brand-200" :class="on ? 'bg-ok-500' : 'bg-ink-200'" aria-hidden="true">
                                        <span class="size-[22px] rounded-full bg-white shadow-md transition-transform" :class="on && 'translate-x-5'"></span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                        <div x-show="dirty" x-cloak x-transition class="flex items-center justify-between gap-3 border-t border-ink-100 px-5 py-3.5 sm:px-6">
                            <span class="text-[13px] text-ink-600">Modifications non enregistrées</span>
                            <button class="btn-primary min-h-10 px-5 text-sm">Enregistrer</button>
                        </div>
                    </form>
                </section>

                {{-- Sécurité --}}
                <section id="securite" class="card scroll-mt-6" x-data="{ open: {{ $errors->password->any() ? 'true' : 'false' }}, show: false }">
                    <header class="flex items-center gap-3 border-b border-ink-100 px-5 py-4 sm:px-6">
                        <span class="flex size-9 items-center justify-center rounded-lg bg-brand-50 text-brand-600"><x-icon name="shield" :size="18" /></span>
                        <span class="flex flex-col"><span class="text-[16px] font-bold text-navy-950">Sécurité</span><span class="text-[12.5px] text-ink-500">Votre compte est protégé par un code envoyé à chaque connexion.</span></span>
                    </header>
                    <div class="divide-y divide-ink-100">
                        @foreach ([
                            ['mail', 'Adresse e-mail', $user->email_verified_at ? 'Vérifiée le '.$user->email_verified_at->translatedFormat('j F Y') : 'Sera vérifiée à votre prochaine connexion', $user->email_verified_at ? 'badge-ok' : 'badge-warn', $user->email_verified_at ? 'Vérifiée' : 'À vérifier'],
                            ['shield', 'Code de connexion par e-mail', 'Demandé à chaque connexion, en plus du mot de passe', 'badge-ok', 'Activé'],
                            ['calendar', 'Membre depuis', ucfirst($user->created_at->translatedFormat('j F Y')), null, null],
                        ] as [$icon, $label, $value, $badge, $badgeLabel])
                            <div class="flex items-center gap-4 px-5 py-3.5 sm:px-6">
                                <span class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-ink-50 text-ink-600"><x-icon :name="$icon" :size="17" /></span>
                                <span class="flex min-w-0 flex-1 flex-col"><span class="text-[14.5px] font-semibold text-navy-950">{{ $label }}</span><span class="text-[12.5px] text-ink-500">{{ $value }}</span></span>
                                @if ($badge)<span class="{{ $badge }}">{{ $badgeLabel }}</span>@endif
                            </div>
                        @endforeach
                        <div class="flex items-center gap-4 px-5 py-3.5 sm:px-6">
                            <span class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-ink-50 text-ink-600"><x-icon name="lock" :size="17" /></span>
                            <span class="flex min-w-0 flex-1 flex-col"><span class="text-[14.5px] font-semibold text-navy-950">Mot de passe</span><span class="text-[12.5px] text-ink-500">8 caractères minimum, dont un chiffre</span></span>
                            <button type="button" @click="open = !open" class="btn-secondary min-h-10 px-4 text-sm" x-text="open ? 'Annuler' : 'Changer'">Changer</button>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('account.password') }}" x-show="open" x-cloak x-transition class="flex flex-col gap-4 border-t border-ink-100 bg-ink-50/40 p-5 sm:p-6">
                        @csrf @method('PUT')
                        <label class="flex flex-col gap-1.5"><span class="field-label">Mot de passe actuel</span>
                            <input name="current_password" :type="show ? 'text' : 'password'" type="password" autocomplete="current-password" required class="field">
                            @error('current_password', 'password')<span class="text-[12.5px] font-semibold text-err-700">{{ $message }}</span>@enderror</label>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <label class="flex flex-col gap-1.5"><span class="field-label">Nouveau mot de passe</span>
                                <input name="password" :type="show ? 'text' : 'password'" type="password" autocomplete="new-password" required class="field">
                                @error('password', 'password')<span class="text-[12.5px] font-semibold text-err-700">{{ $message }}</span>@enderror</label>
                            <label class="flex flex-col gap-1.5"><span class="field-label">Confirmer le nouveau mot de passe</span>
                                <input name="password_confirmation" :type="show ? 'text' : 'password'" type="password" autocomplete="new-password" required class="field"></label>
                        </div>
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <label class="flex cursor-pointer items-center gap-2 text-[13px] text-ink-600"><input type="checkbox" x-model="show" class="size-4 accent-brand-600"> Afficher les mots de passe</label>
                            <button class="btn-primary min-h-11 px-6 text-sm">Changer le mot de passe</button>
                        </div>
                    </form>
                </section>

                {{-- Aide --}}
                <section class="bg-hero relative flex flex-col gap-4 overflow-hidden rounded-2xl p-6 text-white sm:flex-row sm:items-center">
                    <div class="bg-weave pointer-events-none absolute inset-y-0 right-0 w-1/3 opacity-50 [mask-image:linear-gradient(to_left,black,transparent)]"></div>
                    <span class="glass relative flex size-12 shrink-0 items-center justify-center rounded-2xl text-gold-300"><x-icon name="help" :size="24" /></span>
                    <span class="relative flex flex-1 flex-col gap-1">
                        <span class="font-display text-lg font-bold">Une question sur votre compte ?</span>
                        <span class="text-[13.5px] text-white/70">L’équipe Travel Express répond dans la communauté ou par le formulaire de contact.</span>
                    </span>
                    <a href="{{ route('chat.show', [\App\Models\Conversation::superGroup(), 'theme' => 'entraide']) }}" class="btn-light relative min-h-11 text-sm">Poser une question</a>
                </section>

                {{-- Suppression --}}
                @unless ($user->isStaff())
                    <section id="suppression" class="card scroll-mt-6 ring-err-700/15" x-data="{ open: {{ $errors->deletion->any() ? 'true' : 'false' }} }">
                        <div class="flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:p-6">
                            <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-err-50 text-err-700"><x-icon name="trash" :size="19" /></span>
                            <span class="flex flex-1 flex-col gap-0.5"><span class="text-[15px] font-bold text-navy-950">Supprimer mon compte</span><span class="text-[13px] leading-relaxed text-ink-600">Votre profil, votre dossier, vos documents et vos messages seront définitivement effacés.</span></span>
                            <button type="button" @click="open = !open" class="btn min-h-10 shrink-0 bg-err-50 px-4 text-sm text-err-700 hover:bg-err-500 hover:text-white" x-text="open ? 'Annuler' : 'Supprimer'">Supprimer</button>
                        </div>
                        <form method="POST" action="{{ route('account.destroy') }}" x-show="open" x-cloak x-transition class="flex flex-col gap-4 border-t border-err-700/10 bg-err-50/50 p-5 sm:p-6">
                            @csrf @method('DELETE')
                            <p class="text-[13.5px] font-semibold text-err-700">Cette action est irréversible. Si vous avez un dossier en cours, prévenez d’abord votre conseiller.</p>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <label class="flex flex-col gap-1.5"><span class="field-label">Votre mot de passe</span>
                                    <input type="password" name="password" autocomplete="current-password" required class="field">
                                    @error('password', 'deletion')<span class="text-[12.5px] font-semibold text-err-700">{{ $message }}</span>@enderror</label>
                                <label class="flex flex-col gap-1.5"><span class="field-label">Tapez SUPPRIMER pour confirmer</span>
                                    <input name="confirmation" autocomplete="off" required class="field uppercase">
                                    @error('confirmation', 'deletion')<span class="text-[12.5px] font-semibold text-err-700">{{ $message }}</span>@enderror</label>
                            </div>
                            <button class="btn self-start bg-err-500 px-6 text-sm text-white hover:brightness-95">Supprimer définitivement mon compte</button>
                        </form>
                    </section>
                @endunless
            </div>
        </div>
    </div>
</x-layouts.app>
