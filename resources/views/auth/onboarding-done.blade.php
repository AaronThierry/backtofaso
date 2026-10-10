<x-layouts.auth title="Compte créé">
    <div class="flex flex-1 flex-col gap-7 pt-10">
        <div class="flex flex-col items-center gap-4 text-center">
            <span class="flex size-[72px] items-center justify-center rounded-full bg-ok-50 text-ok-700"><x-icon name="check" :size="34" stroke-width="2.4" /></span>
            <h1 class="text-[28px] font-bold leading-tight">Bienvenue, {{ $user->first_name }}</h1>
            <p class="max-w-xs text-[15px] leading-relaxed text-ink-600">Votre compte est créé. Un conseiller Travel Express vous contactera très bientôt.</p>
        </div>

        <div class="flex flex-col gap-2.5">
            <span class="text-[13px] font-semibold text-ink-600">Pour la suite</span>
            @php
                $next = [];
                if ($user->needsDossier()) $next[] = [route('dossier'), 'Déposer vos documents', 'Passeport, diplômes, relevés de notes'];
                $next[] = [route('chat.index'), 'Dire bonjour à la communauté', 'Vous êtes déjà membre du super groupe'];
                if ($user->talentProfile) $next[] = [route('talents.edit'), 'Compléter votre profil talent', 'Compétences, langues, CV'];
                if ($user->isRecruiter()) $next[] = [route('talents.index'), 'Parcourir le vivier de talents', 'Profils formés en Chine'];
            @endphp
            @foreach ($next as $i => [$url, $title, $sub])
                <a href="{{ $url }}" class="card flex items-center gap-3.5 p-3.5">
                    <span @class(['flex size-8 shrink-0 items-center justify-center rounded-full text-sm font-bold', 'bg-brand-600 text-white' => $i === 0, 'bg-brand-100 text-brand-700' => $i > 0])>{{ $i + 1 }}</span>
                    <span class="flex flex-1 flex-col gap-0.5"><span class="text-[15px] font-bold">{{ $title }}</span><span class="text-[13px] text-ink-600">{{ $sub }}</span></span>
                    <x-icon name="chevron" :size="18" class="text-ink-600" />
                </a>
            @endforeach
        </div>

        <div class="mt-auto flex flex-col gap-1.5">
            <a href="{{ $next[0][0] }}" class="btn-primary min-h-[52px] text-base">Commencer</a>
            <a href="{{ route('dashboard') }}" class="btn-ghost">Aller à l’accueil</a>
        </div>
    </div>
</x-layouts.auth>
