@php($currentRole = old('role', $role))
<x-layouts.auth title="Créer un compte">
    <form method="POST" action="{{ route('register.store') }}" class="flex flex-1 flex-col gap-5"
          x-data="{ show: false, terms: {{ old('terms') ? 'true' : 'false' }}, password: '', loading: false,
                    get strength() { let s = 0; if (this.password.length >= 8) s++; if (/\d/.test(this.password)) s++; if (/[A-Z]/.test(this.password) || /[^\w]/.test(this.password)) s++; if (this.password.length >= 12) s++; return s; } }"
          @submit="loading = true">
        @csrf
        <input type="hidden" name="role" value="{{ $currentRole }}">

        <div class="flex items-center gap-4">
            <a href="{{ route('welcome') }}" aria-label="Retour" class="icon-btn"><x-icon name="back" /></a>
            <x-progress-steps :current="1" :total="4" class="flex-1" />
        </div>

        <div class="flex flex-col gap-2">
            <h1 class="text-[30px] font-extrabold leading-[1.05] text-navy-950 lg:text-[36px]">Créez votre compte</h1>
            <p class="text-[15px] leading-relaxed text-ink-600">Il vous servira à suivre votre dossier et à échanger avec la communauté.</p>
        </div>

        <div class="flex items-center gap-3 rounded-2xl bg-linear-to-r from-brand-50 to-white px-3.5 py-3 ring-1 ring-brand-100">
            <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-brand-600 text-white shadow-glow"><x-icon :name="\App\Models\User::ROLE_ICONS[$currentRole] ?? 'user'" :size="19" /></span>
            <span class="flex min-w-0 flex-1 flex-col">
                <span class="text-xs font-semibold text-ink-500">Votre profil</span>
                <span class="truncate text-[15px] font-bold text-navy-950">{{ $roles[$currentRole][0] }}</span>
            </span>
            <a href="{{ route('welcome') }}" class="rounded-lg px-2.5 py-1.5 text-[13px] font-bold text-brand-600 hover:bg-brand-100">Modifier</a>
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
            <span class="field-label">Adresse e-mail</span>
            <span class="field-group">
                <x-icon name="mail" :size="19" class="ml-3.5 text-ink-400" />
                <input name="email" type="email" inputmode="email" value="{{ old('email', $old['email'] ?? '') }}" autocomplete="email" placeholder="vous@exemple.com" required class="min-w-0 flex-1 bg-transparent px-3 text-[15px] outline-none placeholder:text-ink-400">
            </span>
            <span class="text-[12.5px] text-ink-600">Elle servira à vous connecter. Nous y enverrons un code pour la vérifier.</span>
            <x-field-error name="email" />
        </label>

        <label class="flex flex-col gap-1.5">
            <span class="field-label">Téléphone</span>
            <span class="field-group">
                <span class="flex h-full items-center gap-1.5 self-stretch border-r border-ink-200 bg-ink-50 px-3.5 text-[15px] font-semibold text-ink-800">+226</span>
                <input name="phone" value="{{ old('phone', isset($old['phone']) ? preg_replace('/^\+226/', '', $old['phone']) : '') }}" inputmode="tel" autocomplete="tel-national" placeholder="70 00 00 00" required class="min-w-0 flex-1 bg-transparent px-3.5 text-[15px] outline-none placeholder:text-ink-400">
            </span>
            <span class="text-[12.5px] text-ink-600">Pour que votre conseiller puisse vous joindre.</span>
            <x-field-error name="phone" /><x-field-error name="phone_full" />
        </label>

        <label class="flex flex-col gap-1.5">
            <span class="field-label">Mot de passe</span>
            <span class="field-group pr-1">
                <x-icon name="lock" :size="19" class="ml-3.5 text-ink-400" />
                <input name="password" x-model="password" :type="show ? 'text' : 'password'" type="password" autocomplete="new-password" required class="min-w-0 flex-1 bg-transparent px-3 text-[15px] outline-none">
                <button type="button" @click="show = !show" :aria-label="show ? 'Masquer le mot de passe' : 'Afficher le mot de passe'" class="flex size-10 items-center justify-center rounded-lg text-ink-500 hover:bg-ink-50 hover:text-ink-900"><x-icon name="eye" :size="19" /></button>
            </span>
            <span class="grid grid-cols-4 gap-1.5 pt-1" aria-hidden="true">
                <template x-for="i in 4" :key="i">
                    <span class="h-1 rounded-full transition-colors duration-300"
                          :class="i <= strength ? (strength <= 1 ? 'bg-err-500' : strength === 2 ? 'bg-warn-500' : 'bg-ok-500') : 'bg-ink-100'"></span>
                </template>
            </span>
            <span class="text-[12.5px] text-ink-600" x-text="password.length === 0 ? '8 caractères minimum, dont un chiffre.' : (strength <= 1 ? 'Trop faible : 8 caractères minimum, dont un chiffre.' : strength === 2 ? 'Correct. Ajoutez une majuscule ou un symbole pour le renforcer.' : 'Mot de passe solide.')">8 caractères minimum, dont un chiffre.</span>
            <x-field-error name="password" />
        </label>

        <label class="flex cursor-pointer items-start gap-3 rounded-xl p-1">
            <input type="checkbox" name="terms" value="1" x-model="terms" class="mt-0.5 size-5 shrink-0 rounded accent-brand-600">
            <span class="text-[13.5px] leading-snug text-ink-700">J’accepte les <a href="{{ route('legal.terms') }}" target="_blank" class="font-semibold text-brand-600 underline-offset-2 hover:underline">conditions d’utilisation</a> et la <a href="{{ route('legal.privacy') }}" target="_blank" class="font-semibold text-brand-600 underline-offset-2 hover:underline">politique de confidentialité</a>.</span>
        </label>
        <x-field-error name="terms" />

        <div class="mt-auto flex flex-col items-center gap-3 pt-2">
            <button type="submit" class="btn-primary min-h-[54px] w-full text-base" :disabled="!terms || loading">
                <span x-show="!loading" class="flex items-center gap-2">Recevoir mon code par e-mail <x-icon name="arrow" :size="18" /></span>
                <span x-show="loading" x-cloak class="flex items-center gap-2"><span class="size-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span> Un instant…</span>
            </button>
            <span class="text-[13.5px] text-ink-600">Déjà inscrit ? <a href="{{ route('login') }}" class="font-bold text-brand-600 hover:underline">Se connecter</a></span>
        </div>
    </form>
</x-layouts.auth>
