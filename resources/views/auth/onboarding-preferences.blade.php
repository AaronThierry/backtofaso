<x-layouts.auth title="Dernière étape">
    <form method="POST" action="{{ route('onboarding.preferences.store') }}" enctype="multipart/form-data" class="flex flex-1 flex-col gap-5"
          x-data="{ preview: null }">
        @csrf
        <div class="flex items-center gap-3">
            <a href="{{ route('onboarding.profile') }}" aria-label="Retour" class="flex size-11 shrink-0 items-center justify-center rounded-full border border-ink-200"><x-icon name="back" /></a>
            <x-progress-steps :current="\App\Services\OtpService::enabled() ? 4 : 3" :total="\App\Services\OtpService::enabled() ? 4 : 3" class="flex-1" />
        </div>

        <div class="flex flex-col gap-1.5">
            <h1 class="text-[26px] font-bold leading-tight">Dernière étape</h1>
            <p class="text-[14.5px] leading-relaxed text-ink-600">Ajoutez une photo et choisissez comment nous vous tenons informé.</p>
        </div>

        <label class="flex cursor-pointer items-center gap-4">
            <span class="flex size-20 shrink-0 items-center justify-center overflow-hidden rounded-full border-2 border-dashed border-ink-500 bg-ink-50 text-ink-600">
                <img x-show="preview" x-cloak :src="preview" alt="" class="size-full object-cover">
                <x-icon name="camera" :size="28" x-show="!preview" />
            </span>
            <span class="flex flex-col gap-1">
                <span class="text-[15px] font-bold">Photo de profil</span>
                <span class="text-[13px] leading-snug text-ink-600">Facultatif. Utile si vous rejoignez le vivier de talents.</span>
            </span>
            <input type="file" name="avatar" accept="image/*" class="sr-only" @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null">
        </label>
        <x-field-error name="avatar" />

        <div class="flex flex-col gap-2">
            <span class="field-label">Recevoir les nouvelles de mon dossier par</span>
            <div class="card divide-y divide-brand-100">
                @foreach ([
                    ['notify_push', 'Notifications', 'Sur ce téléphone'],
                    ['notify_sms', 'SMS', 'Étapes importantes uniquement'],
                    ['notify_whatsapp', 'WhatsApp', 'Messages de votre conseiller'],
                    ['notify_email', 'E-mail', 'Récapitulatifs et documents'],
                ] as [$name, $label, $sub])
                    <label class="flex min-h-[60px] cursor-pointer items-center justify-between gap-3 px-3.5 py-2" x-data="{ on: {{ $user->$name ? 'true' : 'false' }} }">
                        <span class="flex flex-col gap-0.5"><span class="text-[15px] font-semibold">{{ $label }}</span><span class="text-[12.5px] text-ink-600">{{ $sub }}</span></span>
                        <input type="checkbox" name="{{ $name }}" value="1" x-model="on" class="sr-only">
                        <span class="flex h-[31px] w-[51px] shrink-0 rounded-full p-0.5 transition" :class="on ? 'justify-end bg-[#34A853]' : 'justify-start bg-ink-200'" aria-hidden="true">
                            <span class="size-[27px] rounded-full bg-white shadow"></span>
                        </span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="flex gap-3 rounded-xl border border-ink-100 bg-ink-50 p-3.5">
            <x-logo :size="40" />
            <span class="text-[13.5px] leading-normal">Vous rejoignez automatiquement la <b>Communauté Back to Burkina</b>, le groupe de tous les membres.</span>
        </div>

        <button type="submit" class="btn-primary mt-auto min-h-[52px] text-base">Créer mon compte</button>
    </form>
</x-layouts.auth>
