<x-layouts.auth title="Connexion">
    <div class="flex flex-1 flex-col gap-[22px]">
        <a href="{{ route('home') }}" aria-label="Retour" class="flex size-11 items-center justify-center rounded-full border border-ink-200"><x-icon name="back" /></a>

        <div class="flex flex-col gap-3.5">
            <x-logo :size="52" />
            <h1 class="text-[28px] font-bold leading-tight">Bon retour parmi nous</h1>
            <p class="text-[14.5px] leading-relaxed text-ink-600">Connectez-vous pour suivre votre dossier et retrouver la communauté.</p>
        </div>

        <x-flash />

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-[18px]" id="login-form">
            @csrf
            <label class="flex flex-col gap-1.5">
                <span class="field-label">Téléphone ou e-mail</span>
                <input name="login" value="{{ old('login') }}" autocomplete="username" placeholder="70 00 00 00" required class="field">
                <x-field-error name="login" />
            </label>
            <label class="flex flex-col gap-1.5">
                <span class="field-label">Mot de passe</span>
                <input type="password" name="password" autocomplete="current-password" required class="field">
            </label>
            <button type="submit" class="btn-primary min-h-[52px] text-base">Se connecter</button>
        </form>

        <div class="flex items-center gap-3 text-[13px] text-ink-600"><span class="h-px flex-1 bg-ink-100"></span>ou<span class="h-px flex-1 bg-ink-100"></span></div>

        <form method="POST" action="{{ route('login.code') }}" x-data @submit="$refs.login.value = document.querySelector('#login-form [name=login]').value">
            @csrf
            <input type="hidden" name="login" x-ref="login">
            <button type="submit" class="btn-secondary min-h-[52px] w-full"><x-icon name="phone" /> Recevoir un code par SMS</button>
        </form>

        <p class="mt-auto pt-6 text-center text-sm text-ink-600">Pas encore de compte ? <a href="{{ route('welcome') }}" class="font-bold text-brand-600">Créer un compte</a></p>
    </div>
</x-layouts.auth>
