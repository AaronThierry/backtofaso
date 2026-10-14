@php
    $group = $invitation?->conversation;
    $masked = $invitation ? \App\Services\OtpService::maskEmail($invitation->email) : null;
@endphp
<x-layouts.auth title="Invitation">
    <div class="flex flex-1 flex-col gap-7">
        <div class="flex items-center justify-between">
            <a href="{{ route('home') }}" aria-label="Accueil" class="icon-btn"><x-icon name="back" /></a>
            <a href="{{ route('home') }}" class="flex items-center gap-2 lg:hidden"><x-logo :size="36" /><span class="font-display font-bold">Back to Burkina</span></a>
        </div>

        @if (in_array($state, ['invalid', 'used', 'expired'], true))
            {{-- Lien inutilisable --}}
            <div class="flex flex-col gap-4">
                <span class="flex size-16 items-center justify-center rounded-2xl bg-warn-50 text-warn-700 ring-1 ring-warn-700/15"><x-icon name="clock" :size="30" /></span>
                <h1 class="text-[30px] font-extrabold leading-[1.05] text-navy-950">
                    {{ ['invalid' => 'Ce lien d’invitation n’est pas valable', 'used' => 'Cette invitation a déjà été utilisée', 'expired' => 'Cette invitation a expiré'][$state] }}
                </h1>
                <p class="text-[15px] leading-relaxed text-ink-600">
                    @if ($state === 'expired')
                        Les invitations sont valables {{ \App\Models\GroupInvitation::VALID_DAYS }} jours. Demandez à {{ $invitation->inviter?->first_name ?? 'la personne qui vous a invité' }} de vous en envoyer une nouvelle.
                    @elseif ($state === 'used')
                        Chaque lien ne sert qu’une fois. Si c’était vous, connectez-vous : le groupe se trouve dans vos messages.
                    @else
                        Le lien est peut-être incomplet ou a été remplacé par une invitation plus récente. Vérifiez le dernier e-mail reçu.
                    @endif
                </p>
            </div>
            <div class="mt-auto flex flex-col gap-2.5 lg:mt-2">
                @auth
                    <a href="{{ route('chat.index') }}" class="btn-primary min-h-[54px] text-base">Aller à mes messages</a>
                @else
                    <a href="{{ route('login') }}" class="btn-primary min-h-[54px] text-base">Se connecter</a>
                @endauth
                <a href="{{ route('home') }}" class="btn-ghost">Retour à l’accueil</a>
            </div>
        @else
            {{-- Carte du groupe --}}
            <div class="flex flex-col gap-2.5">
                <span class="eyebrow">Invitation</span>
                <h1 class="text-[30px] font-extrabold leading-[1.05] text-navy-950 lg:text-[36px]">Rejoignez le groupe « {{ $group->name }} »</h1>
                <p class="text-[15px] leading-relaxed text-ink-600"><b class="text-ink-900">{{ $invitation->inviter?->name ?? 'Un membre' }}</b> vous invite dans ce groupe {{ $group->is_public ? 'public' : 'privé' }} de la communauté Back to Burkina.</p>
            </div>

            <div class="card flex items-center gap-4 p-4">
                @if ($group->avatarUrl())
                    <img src="{{ $group->avatarUrl() }}" alt="" class="size-14 rounded-[30%] object-cover">
                @else
                    <span class="flex size-14 shrink-0 items-center justify-center rounded-[30%] bg-hero font-display text-lg font-bold text-white">{{ mb_strtoupper(mb_substr($group->name, 0, 2)) }}</span>
                @endif
                <span class="flex min-w-0 flex-1 flex-col gap-0.5">
                    <span class="truncate font-display text-[17px] font-bold text-navy-950">{{ $group->name }}</span>
                    <span class="text-[13px] text-ink-500">{{ $group->users()->count() }} membre{{ $group->users()->count() > 1 ? 's' : '' }} · groupe {{ $group->is_public ? 'public' : 'privé' }}</span>
                    @if ($group->description)<span class="line-clamp-2 text-[13px] text-ink-600">{{ $group->description }}</span>@endif
                </span>
            </div>

            <div class="flex items-start gap-3 rounded-2xl bg-gold-50 p-4 text-[13.5px] leading-relaxed text-gold-700 ring-1 ring-gold-200">
                <x-icon name="lock" :size="18" class="mt-0.5 shrink-0" />
                <span>Invitation personnelle pour <b>{{ $masked }}</b>, valable jusqu’au {{ $invitation->expires_at->translatedFormat('j F Y') }}.</span>
            </div>

            <div class="mt-auto flex flex-col gap-2.5 lg:mt-2">
                @if ($state === 'ready')
                    <form method="POST" action="{{ route('invitations.accept', $token) }}" x-data="{ sending: false }" @submit="sending = true">@csrf
                        <button class="btn-primary min-h-[54px] w-full text-base" :disabled="sending">
                            <span x-show="!sending" class="flex items-center gap-2">Rejoindre le groupe <x-icon name="arrow" :size="18" /></span>
                            <span x-show="sending" x-cloak class="flex items-center gap-2"><span class="size-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span> Un instant…</span>
                        </button>
                    </form>
                    <a href="{{ route('chat.index') }}" class="btn-ghost">Plus tard</a>
                @elseif ($state === 'wrong-account')
                    <div class="flex items-start gap-3 rounded-2xl bg-err-50 p-4 text-[13.5px] leading-relaxed text-err-700 ring-1 ring-err-700/15">
                        <x-icon name="help" :size="18" class="mt-0.5 shrink-0" />
                        <span>Vous êtes connecté avec <b>{{ \App\Services\OtpService::maskEmail(auth()->user()->email) }}</b>. Cette invitation ne fonctionne qu’avec le compte {{ $masked }}.</span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">@csrf
                        <input type="hidden" name="next" value="{{ route('invitations.show', $token, false) }}">
                        <button class="btn-primary min-h-[54px] w-full text-base">Changer de compte</button>
                    </form>
                    <a href="{{ route('chat.index') }}" class="btn-ghost">Rester sur ce compte</a>
                @else
                    @if ($hasAccount)
                        <a href="{{ route('login', ['email' => $invitation->email]) }}" class="btn-primary min-h-[54px] text-base">Se connecter pour rejoindre <x-icon name="arrow" :size="18" /></a>
                        <p class="text-center text-[13px] text-ink-500">Vous reviendrez sur cette page juste après la connexion.</p>
                    @else
                        <a href="{{ route('welcome', ['email' => $invitation->email]) }}" class="btn-primary min-h-[54px] text-base">Créer mon compte <x-icon name="arrow" :size="18" /></a>
                        <p class="text-center text-[13px] text-ink-500">Inscrivez-vous avec cette adresse : vous rejoindrez le groupe automatiquement.</p>
                        <a href="{{ route('login', ['email' => $invitation->email]) }}" class="btn-ghost">J’ai déjà un compte</a>
                    @endif
                @endif
            </div>
        @endif
    </div>
</x-layouts.auth>
