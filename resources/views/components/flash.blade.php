@if (session('status'))
    <div x-data="{ show: true }" x-show="show" x-transition.opacity.duration.300ms role="status"
         class="flex animate-rise items-start gap-3 rounded-2xl bg-ok-50 px-4 py-3.5 text-sm font-semibold text-ok-700 ring-1 ring-ok-700/15">
        <span class="mt-px flex size-5 shrink-0 items-center justify-center rounded-full bg-ok-500 text-white"><x-icon name="check" :size="12" stroke-width="3.2" /></span>
        <span class="flex-1">{{ session('status') }}</span>
        <button type="button" @click="show = false" aria-label="Fermer" class="-m-1 rounded-full p-1 opacity-60 hover:opacity-100"><x-icon name="close" :size="16" /></button>
    </div>
@endif
@if (session('dev_code'))
    <div class="flex items-center gap-3 rounded-2xl bg-gold-50 px-4 py-3.5 text-sm text-gold-700 ring-1 ring-gold-300" role="status">
        <x-icon name="lock" :size="18" />
        <span>Mode développement : votre code est <b class="font-display text-base tracking-[0.3em]">{{ session('dev_code') }}</b></span>
    </div>
@endif
