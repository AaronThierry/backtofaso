@props(['title' => null])
{{-- Écrans d'inscription / connexion : plein écran sur mobile, panneau + formulaire sur ordinateur --}}
<x-layouts.base :title="$title">
    <div class="flex min-h-dvh flex-wrap">
        <aside class="hidden flex-col gap-9 bg-brand-600 p-12 text-white lg:flex lg:w-[38%] xl:p-16">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                <x-logo :size="40" />
                <span class="text-lg font-bold">Back to Burkina</span>
            </a>
            <div class="flex flex-col gap-3.5">
                <h2 class="text-4xl font-bold leading-tight">Un compte pour tout votre parcours.</h2>
                <p class="max-w-md text-base leading-relaxed">De la candidature au retour à l’emploi, tout se passe au même endroit, sur le web comme sur l’application.</p>
            </div>
            <ul class="flex flex-col gap-4">
                @foreach ([
                    ['Suivi du dossier en temps réel', 'Admission, JW202, visa : chaque étape visible.'],
                    ['Un conseiller dédié', 'Des réponses par message, sans vous déplacer.'],
                    ['La communauté Back to Burkina', 'Étudiants, diplômés et recruteurs réunis.'],
                ] as [$t, $d])
                    <li class="flex items-start gap-3">
                        <span class="flex size-7 shrink-0 items-center justify-center rounded-full bg-white text-brand-600"><x-icon name="check" :size="14" stroke-width="3" /></span>
                        <span class="flex flex-col gap-0.5"><span class="font-bold">{{ $t }}</span><span class="text-sm">{{ $d }}</span></span>
                    </li>
                @endforeach
            </ul>
            <span class="mt-auto text-[13px]">Un service Travel Express · Bobo-Dioulasso</span>
        </aside>

        <main class="flex min-h-dvh flex-1 justify-center bg-white">
            <div class="relative flex w-full max-w-xl flex-col px-5 pb-[calc(env(safe-area-inset-bottom)+24px)] pt-[calc(env(safe-area-inset-top)+20px)] sm:px-8 lg:pt-14">
                {{ $slot }}
            </div>
        </main>
    </div>
</x-layouts.base>
