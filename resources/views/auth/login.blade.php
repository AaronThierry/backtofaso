<x-layouts.auth title="Connexion">
    <div class="flex flex-1 flex-col gap-7">
        <div class="flex items-center justify-between">
            <a href="{{ route('home') }}" aria-label="Retour à l’accueil" class="icon-btn"><x-icon name="back" /></a>
            <a href="{{ route('home') }}" class="flex items-center gap-2 lg:hidden"><x-logo :size="36" /><span class="font-display font-bold">Back to Burkina</span></a>
        </div>

        <div class="flex flex-col gap-2.5">
            <span class="eyebrow">Espace membre</span>
            <h1 class="text-[32px] font-extrabold leading-[1.05] text-navy-950 lg:text-[38px]">Bon retour parmi nous</h1>
            <p class="text-[15px] leading-relaxed text-ink-600">Connectez-vous pour suivre votre dossier et retrouver la communauté.</p>
        </div>

        <x-flash />

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-5" id="login-form" x-data="{ show: false, loading: false }" @submit="loading = true">
            @csrf
            <label class="flex flex-col gap-1.5">
                <span class="field-label">Téléphone ou e-mail</span>
                <span class="field-group">
                    <x-icon name="user" :size="19" class="ml-3.5 text-ink-400" />
                    <input name="login" value="{{ old('login') }}" autocomplete="username" placeholder="70 00 00 00 ou vous@exemple.com" required autofocus class="min-w-0 flex-1 bg-transparent px-3 text-[15px] outline-none placeholder:text-ink-400">
                </span>
                <x-field-error name="login" />
            </label>
            <label class="flex flex-col gap-1.5">
                <span class="field-label">Mot de passe</span>
                <span class="field-group pr-1">
                    <x-icon name="lock" :size="19" class="ml-3.5 text-ink-400" />
                    <input name="password" :type="show ? 'text' : 'password'" type="password" autocomplete="current-password" required class="min-w-0 flex-1 bg-transparent px-3 text-[15px] outline-none">
                    <button type="button" @click="show = !show" :aria-label="show ? 'Masquer le mot de passe' : 'Afficher le mot de passe'" class="flex size-10 items-center justify-center rounded-lg text-ink-500 hover:bg-ink-50 hover:text-ink-900">
                        <x-icon name="eye" :size="19" />
                    </button>
                </span>
            </label>
            <button type="submit" class="btn-primary mt-1 min-h-[54px] text-base" :disabled="loading">
                <span x-show="!loading">Se connecter</span>
                <span x-show="loading" x-cloak class="flex items-center gap-2"><span class="size-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span> Connexion…</span>
            </button>
        </form>

        @if (\App\Services\OtpService::enabled())
            <div class="flex items-center gap-3 text-[13px] text-ink-500"><span class="h-px flex-1 bg-ink-200"></span>ou<span class="h-px flex-1 bg-ink-200"></span></div>

            <form method="POST" action="{{ route('login.code') }}" x-data @submit="$refs.login.value = document.querySelector('#login-form [name=login]').value">
                @csrf
                <input type="hidden" name="login" x-ref="login">
                <button type="submit" class="btn-secondary min-h-[52px] w-full"><x-icon :name="\App\Services\OtpService::usesMail() ? 'mail' : 'phone'" /> {{ \App\Services\OtpService::usesMail() ? 'Recevoir un code par e-mail' : 'Recevoir un code par SMS' }}</button>
            </form>
        @endif

        <div class="mt-auto flex items-center justify-between gap-3 rounded-2xl bg-ink-50 px-4 py-3.5 ring-1 ring-ink-100 lg:mt-4">
            <span class="text-sm text-ink-600">Pas encore de compte ?</span>
            <a href="{{ route('welcome') }}" class="inline-flex items-center gap-1.5 text-sm font-bold text-brand-600 hover:gap-2.5 transition-all">Créer un compte <x-icon name="arrow" :size="16" /></a>
        </div>
    </div>
</x-layouts.auth>
