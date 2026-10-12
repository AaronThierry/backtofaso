<x-layouts.auth title="Vérification du numéro">
    <div class="flex flex-1 flex-col gap-5"
         x-data="otp({{ config('backtoburkina.otp.length') }}, {{ (int) $wait }})">
        <div class="flex items-center gap-3">
            <a href="{{ $backUrl }}" aria-label="Retour" class="flex size-11 shrink-0 items-center justify-center rounded-full border border-ink-200"><x-icon name="back" /></a>
            @if ($step)
                <x-progress-steps :current="$step" class="flex-1" />
            @endif
        </div>

        <div class="flex flex-col gap-1.5">
            <h1 class="text-[26px] font-bold leading-tight">Saisissez votre code</h1>
            <p class="text-[14.5px] leading-relaxed text-ink-600">Saisissez le code à {{ config('backtoburkina.otp.length') }} chiffres envoyé {{ $via }} <b class="text-ink-900">{{ $destination }}</b>.@if (str_contains($via, 'e-mail')) Pensez à regarder dans les courriers indésirables.@endif</p>
        </div>

        <x-flash />

        <form method="POST" action="{{ $action }}" x-ref="form" class="flex flex-col gap-5">
            @csrf
            <input type="hidden" name="code" :value="code">
            {{-- Champ réel : clavier numérique natif + remplissage automatique du SMS sur iOS / Android --}}
            <label class="relative block">
                <span class="sr-only">Code de vérification</span>
                <input x-ref="input" x-model="code" @input="code = code.replace(/\D/g, '').slice(0, len); if (code.length === len) $nextTick(() => $refs.form.submit())"
                       inputmode="numeric" autocomplete="one-time-code" maxlength="{{ config('backtoburkina.otp.length') }}" autofocus
                       class="absolute inset-0 z-10 h-full w-full opacity-0">
                <span class="grid gap-2" style="grid-template-columns: repeat({{ config('backtoburkina.otp.length') }}, minmax(0, 1fr))">
                    <template x-for="i in len" :key="i">
                        <span class="flex h-14 items-center justify-center rounded-[10px] bg-white text-[22px] font-bold"
                              :class="i - 1 === code.length ? 'border-2 border-brand-600' : 'border border-ink-500'" x-text="code[i - 1] ?? ''"></span>
                    </template>
                </span>
            </label>
            <x-field-error name="code" />

            <div class="flex items-center justify-between text-[13.5px]">
                @if ($resendAction)
                    <span x-show="wait > 0" class="text-ink-600">Renvoyer le code dans <span x-text="mmss"></span></span>
                    <button x-show="wait <= 0" x-cloak type="submit" form="resend" class="font-bold text-brand-600">Renvoyer le code</button>
                @else
                    <span></span>
                @endif
                <a href="{{ $backUrl }}" class="font-bold text-brand-600">Changer de numéro</a>
            </div>

            <button type="submit" class="btn-primary min-h-[52px] text-base" :disabled="code.length < len" :class="code.length < len && 'opacity-50'">Vérifier</button>
        </form>

        @if ($resendAction)
            <form id="resend" method="POST" action="{{ $resendAction }}">@csrf</form>
        @endif
    </div>

    @push('scripts')
        <script>
            function otp(len, wait) {
                return {
                    len, code: '', wait,
                    get mmss() { return Math.floor(this.wait / 60) + ':' + String(this.wait % 60).padStart(2, '0'); },
                    init() { setInterval(() => { if (this.wait > 0) this.wait--; }, 1000); },
                };
            }
        </script>
    @endpush
</x-layouts.auth>
