<x-layouts.auth title="Vérification">
    <div class="flex flex-1 flex-col gap-6"
         x-data="otp({{ config('backtoburkina.otp.length') }}, {{ (int) $wait }})">
        <div class="flex items-center gap-4">
            <a href="{{ $backUrl }}" aria-label="Retour" class="icon-btn"><x-icon name="back" /></a>
            @if ($step)
                <x-progress-steps :current="$step" class="flex-1" />
            @endif
        </div>

        <div class="flex flex-col gap-3">
            <span class="flex size-14 items-center justify-center rounded-2xl bg-linear-to-br from-brand-50 to-brand-100 text-brand-600 ring-1 ring-brand-100">
                <x-icon :name="str_contains($via, 'e-mail') ? 'mail' : 'phone'" :size="26" />
            </span>
            <h1 class="text-[30px] font-extrabold leading-[1.05] text-navy-950 lg:text-[36px]">Saisissez votre code</h1>
            <p class="text-[15px] leading-relaxed text-ink-600">Nous avons envoyé un code à {{ config('backtoburkina.otp.length') }} chiffres {{ $via }} <b class="text-ink-900">{{ $destination }}</b>.@if (str_contains($via, 'e-mail')) Pensez à regarder dans les courriers indésirables.@endif</p>
        </div>

        <x-flash />

        <form method="POST" action="{{ $action }}" x-ref="form" class="flex flex-col gap-5">
            @csrf
            <input type="hidden" name="code" :value="code">
            {{-- Champ réel : clavier numérique natif + remplissage automatique du SMS sur iOS / Android --}}
            <label class="relative block">
                <span class="sr-only">Code de vérification</span>
                <input x-ref="input" x-model="code" @input="code = code.replace(/\D/g, '').slice(0, len); if (code.length === len) $nextTick(() => $refs.form.submit())"
                       @focus="focused = true" @blur="focused = false"
                       inputmode="numeric" autocomplete="one-time-code" maxlength="{{ config('backtoburkina.otp.length') }}" autofocus
                       class="absolute inset-0 z-10 h-full w-full opacity-0">
                <span class="grid gap-2" style="grid-template-columns: repeat({{ config('backtoburkina.otp.length') }}, minmax(0, 1fr))">
                    <template x-for="i in len" :key="i">
                        <span class="flex h-16 items-center justify-center rounded-2xl bg-white font-display text-[26px] font-bold text-navy-950 shadow-xs transition"
                              :class="focused && i - 1 === code.length ? 'ring-4 ring-brand-100 border-2 border-brand-500' : (code[i - 1] ? 'border-2 border-brand-200' : 'border border-ink-200')" x-text="code[i - 1] ?? ''"></span>
                    </template>
                </span>
            </label>
            <x-field-error name="code" />

            <div class="flex items-center justify-between text-[13.5px]">
                @if ($resendAction)
                    <span x-show="wait > 0" class="flex items-center gap-1.5 text-ink-600"><x-icon name="clock" :size="15" /> Renvoyer dans <span class="font-semibold tabular-nums text-ink-900" x-text="mmss"></span></span>
                    <button x-show="wait <= 0" x-cloak type="submit" form="resend" class="font-bold text-brand-600 hover:underline">Renvoyer le code</button>
                @else
                    <span></span>
                @endif
                <a href="{{ $backUrl }}" class="font-bold text-brand-600 hover:underline">Modifier mes informations</a>
            </div>

            <button type="submit" class="btn-primary min-h-[54px] text-base" :disabled="code.length < len">Vérifier</button>
        </form>

        @if ($resendAction)
            <form id="resend" method="POST" action="{{ $resendAction }}">@csrf</form>
        @endif
    </div>

    @push('scripts')
        <script>
            function otp(len, wait) {
                return {
                    len, code: '', wait, focused: true,
                    get mmss() { return Math.floor(this.wait / 60) + ':' + String(this.wait % 60).padStart(2, '0'); },
                    init() { setInterval(() => { if (this.wait > 0) this.wait--; }, 1000); },
                };
            }
        </script>
    @endpush
</x-layouts.auth>
