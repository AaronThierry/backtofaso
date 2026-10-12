<x-layouts.base title="Hors ligne" body-class="bg-canvas">
    <div class="bg-dots flex min-h-dvh flex-col items-center justify-center gap-5 px-6 text-center">
        <span class="relative flex size-24 items-center justify-center">
            <span class="absolute inset-0 rotate-6 rounded-[28px] bg-gold-200"></span>
            <x-logo :size="88" class="relative" />
        </span>
        <h1 class="mt-2 text-3xl font-extrabold text-navy-950">Vous êtes hors ligne</h1>
        <p class="max-w-xs leading-relaxed text-ink-600">Vérifiez votre connexion internet, puis réessayez. Vos informations vous attendent.</p>
        <a href="/" class="btn-primary mt-1">Réessayer</a>
    </div>
</x-layouts.base>
