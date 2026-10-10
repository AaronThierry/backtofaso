<x-layouts.auth title="Créer un compte">
    <form method="POST" action="{{ route('register.store') }}" class="flex flex-1 flex-col gap-4" x-data="{ show: false, terms: {{ old('terms') ? 'true' : 'false' }} }">
        @csrf
        <input type="hidden" name="role" value="{{ old('role', $role) }}">

        <div class="flex items-center gap-3">
            <a href="{{ route('welcome') }}" aria-label="Retour" class="flex size-11 shrink-0 items-center justify-center rounded-full border border-ink-200"><x-icon name="back" /></a>
            <x-progress-steps :current="1" class="flex-1" />
        </div>

        <div class="flex flex-col gap-1.5">
            <h1 class="text-[26px] font-bold leading-tight">Créez votre compte</h1>
            <p class="text-[14.5px] leading-relaxed text-ink-600">Il vous servira à suivre votre dossier et à échanger avec la communauté.</p>
        </div>

        <div class="flex items-center justify-between rounded-[10px] border border-brand-100 bg-brand-50 px-3.5 py-2.5 text-sm">
            <span><span class="text-ink-600">Profil :</span> <b>{{ $roles[old('role', $role)][0] }}</b></span>
            <a href="{{ route('welcome') }}" class="text-[13.5px] font-bold text-brand-600">Modifier</a>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <label class="flex min-w-0 flex-col gap-1.5">
                <span class="field-label">Prénom</span>
                <input name="first_name" value="{{ old('first_name', $old['first_name'] ?? '') }}" autocomplete="given-name" required class="field">
                <x-field-error name="first_name" />
            </label>
            <label class="flex min-w-0 flex-col gap-1.5">
                <span class="field-label">Nom</span>
                <input name="last_name" value="{{ old('last_name', $old['last_name'] ?? '') }}" autocomplete="family-name" required class="field">
                <x-field-error name="last_name" />
            </label>
        </div>

        <label class="flex flex-col gap-1.5">
            <span class="field-label">Téléphone</span>
            <span class="flex min-h-12 overflow-hidden rounded-[10px] border border-ink-500 focus-within:border-brand-600 focus-within:ring-2 focus-within:ring-brand-100">
                <span class="flex items-center border-r border-ink-200 bg-ink-50 px-3 text-[15px] font-semibold">+226</span>
                <input name="phone" value="{{ old('phone') }}" inputmode="tel" autocomplete="tel-national" placeholder="70 00 00 00" required class="min-w-0 flex-1 px-3.5 text-[15px] outline-none">
            </span>
            <x-field-error name="phone" /><x-field-error name="phone_full" />
        </label>

        <label class="flex flex-col gap-1.5">
            <span class="field-label">E-mail <span class="font-medium text-ink-600">(facultatif)</span></span>
            <input name="email" type="email" value="{{ old('email', $old['email'] ?? '') }}" autocomplete="email" placeholder="vous@exemple.com" class="field">
            <x-field-error name="email" />
        </label>

        <label class="flex flex-col gap-1.5">
            <span class="field-label">Mot de passe</span>
            <span class="flex min-h-12 items-center rounded-[10px] border border-ink-500 pl-3.5 pr-1 focus-within:border-brand-600 focus-within:ring-2 focus-within:ring-brand-100">
                <input name="password" :type="show ? 'text' : 'password'" type="password" autocomplete="new-password" required class="min-w-0 flex-1 text-[15px] outline-none">
                <button type="button" @click="show = !show" class="min-h-10 px-2.5 text-[13.5px] font-bold text-brand-600" x-text="show ? 'Masquer' : 'Afficher'">Afficher</button>
            </span>
            <span class="text-[12.5px] text-ink-600">8 caractères minimum, dont un chiffre.</span>
            <x-field-error name="password" />
        </label>

        <label class="flex cursor-pointer items-start gap-3">
            <input type="checkbox" name="terms" value="1" x-model="terms" class="mt-0.5 size-5 shrink-0 accent-brand-600">
            <span class="text-[13.5px] leading-snug">J’accepte les <a href="#" class="font-semibold text-brand-600">conditions d’utilisation</a> et la <a href="#" class="font-semibold text-brand-600">politique de confidentialité</a>.</span>
        </label>
        <x-field-error name="terms" />

        <div class="mt-auto flex flex-col items-center gap-2.5 pt-4">
            <button type="submit" class="btn-primary min-h-[52px] w-full text-base" :disabled="!terms" :class="!terms && 'opacity-50'">Recevoir le code par SMS</button>
            <span class="text-[13.5px] text-ink-600">Déjà inscrit ? <a href="{{ route('login') }}" class="font-bold text-brand-600">Se connecter</a></span>
        </div>
    </form>
</x-layouts.auth>
