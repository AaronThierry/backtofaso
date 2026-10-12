<x-layouts.auth title="Dernière étape">
    <form method="POST" action="{{ route('onboarding.preferences.store') }}" enctype="multipart/form-data" class="flex flex-1 flex-col gap-6"
          x-data="{ preview: null }">
        @csrf
        <div class="flex items-center gap-4">
            <a href="{{ route('onboarding.profile') }}" aria-label="Retour" class="icon-btn"><x-icon name="back" /></a>
            <x-progress-steps :current="4" :total="4" class="flex-1" />
        </div>

        <div class="flex flex-col gap-2">
            <h1 class="text-[30px] font-extrabold leading-[1.05] text-navy-950 lg:text-[36px]">Dernière étape</h1>
            <p class="text-[15px] leading-relaxed text-ink-600">Ajoutez une photo et choisissez comment nous vous tenons informé.</p>
        </div>

        <label class="card card-hover flex cursor-pointer items-center gap-4 p-4">
            <span class="relative flex size-20 shrink-0 items-center justify-center overflow-hidden rounded-full bg-linear-to-br from-brand-50 to-brand-100 text-brand-600 ring-4 ring-white">
                <img x-show="preview" x-cloak :src="preview" alt="" class="size-full object-cover">
                <x-icon name="camera" :size="28" x-show="!preview" />
            </span>
            <span class="flex flex-1 flex-col gap-1">
                <span class="text-[15px] font-bold text-navy-950" x-text="preview ? 'Belle photo !' : 'Ajouter une photo de profil'">Ajouter une photo de profil</span>
                <span class="text-[13px] leading-snug text-ink-600">Facultatif. Utile si vous rejoignez le vivier de talents.</span>
            </span>
            <span class="hidden rounded-lg bg-brand-50 px-3 py-1.5 text-[13px] font-bold text-brand-600 sm:block" x-text="preview ? 'Changer' : 'Choisir'">Choisir</span>
            <input type="file" name="avatar" accept="image/*" class="sr-only" @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null">
        </label>
        <x-field-error name="avatar" />

        <div class="flex flex-col gap-2.5">
            <span class="field-label">Recevoir les nouvelles de mon dossier par</span>
            <div class="card divide-y divide-ink-100">
                @foreach ([
                    ['notify_push', 'bell', 'Notifications', 'Sur ce téléphone'],
                    ['notify_sms', 'phone', 'SMS', 'Étapes importantes uniquement'],
                    ['notify_whatsapp', 'chat', 'WhatsApp', 'Messages de votre conseiller'],
                    ['notify_email', 'mail', 'E-mail', 'Récapitulatifs et documents'],
                ] as [$name, $icon, $label, $sub])
                    <label class="flex min-h-[64px] cursor-pointer items-center gap-3.5 px-4 py-2.5 transition first:rounded-t-2xl last:rounded-b-2xl hover:bg-ink-50/60" x-data="{ on: {{ $user->$name ? 'true' : 'false' }} }">
                        <span class="flex size-9 shrink-0 items-center justify-center rounded-xl transition" :class="on ? 'bg-brand-50 text-brand-600' : 'bg-ink-50 text-ink-400'"><x-icon :name="$icon" :size="18" /></span>
                        <span class="flex flex-1 flex-col gap-0.5"><span class="text-[15px] font-semibold">{{ $label }}</span><span class="text-[12.5px] text-ink-600">{{ $sub }}</span></span>
                        <input type="checkbox" name="{{ $name }}" value="1" x-model="on" class="peer sr-only">
                        <span class="flex h-[30px] w-[50px] shrink-0 rounded-full p-[3px] transition-colors duration-200 peer-focus-visible:ring-4 peer-focus-visible:ring-brand-200" :class="on ? 'bg-ok-500' : 'bg-ink-200'" aria-hidden="true">
                            <span class="size-6 rounded-full bg-white shadow-md transition-transform duration-200" :class="on && 'translate-x-5'"></span>
                        </span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="bg-hero relative flex items-center gap-3.5 overflow-hidden rounded-2xl p-4 text-white">
            <div class="bg-weave pointer-events-none absolute inset-0 opacity-50"></div>
            <x-logo :size="44" class="relative" />
            <span class="relative text-[13.5px] leading-normal text-white/85">Vous rejoignez automatiquement la <b class="text-white">Communauté Back to Burkina</b>, le groupe de tous les membres.</span>
        </div>

        <button type="submit" class="btn-primary mt-auto min-h-[54px] text-base">Terminer mon inscription <x-icon name="check" :size="18" stroke-width="2.4" /></button>
    </form>
</x-layouts.auth>
