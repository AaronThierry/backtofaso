<x-layouts.auth title="Compte créé">
    <div class="flex flex-1 flex-col gap-8 pt-6">
        <div class="flex flex-col items-center gap-4 text-center">
            <span class="relative flex size-24 items-center justify-center">
                <span class="absolute inset-0 animate-ping rounded-full bg-ok-500/20 [animation-iteration-count:2]"></span>
                <span class="relative flex size-20 items-center justify-center rounded-full bg-linear-to-br from-ok-500 to-ok-700 text-white shadow-[0_14px_30px_-12px_rgb(11_104_52/0.7)]"><x-icon name="check" :size="38" stroke-width="2.6" /></span>
            </span>
            <h1 class="text-[32px] font-extrabold leading-tight text-navy-950 lg:text-[38px]">Bienvenue, {{ $user->first_name }} !</h1>
            <p class="max-w-sm text-[15px] leading-relaxed text-ink-600">Votre compte est créé. Un conseiller Travel Express vous contactera très bientôt.</p>
        </div>

        <div class="flex flex-col gap-2.5">
            <span class="text-[13px] font-bold uppercase tracking-[0.12em] text-ink-500">Pour la suite</span>
            @php
                $next = [];
                if ($user->needsDossier()) $next[] = [route('dossier'), 'folder', 'Déposer vos documents', 'Passeport, diplômes, relevés de notes'];
                $next[] = [route('chat.index'), 'chat', 'Dire bonjour à la communauté', 'Vous êtes déjà membre du super groupe'];
                if ($user->talentProfile) $next[] = [route('talents.edit'), 'briefcase', 'Compléter votre profil talent', 'Compétences, langues, CV'];
                if ($user->isRecruiter()) $next[] = [route('talents.index'), 'users', 'Parcourir le vivier de talents', 'Profils formés à l’étranger'];
            @endphp
            @foreach ($next as $i => [$url, $icon, $title, $sub])
                <a href="{{ $url }}" class="card card-hover group flex items-center gap-4 p-4">
                    <span @class(['flex size-11 shrink-0 items-center justify-center rounded-xl', 'bg-linear-to-br from-brand-500 to-brand-600 text-white shadow-glow' => $i === 0, 'bg-brand-50 text-brand-600' => $i > 0])><x-icon :name="$icon" :size="20" /></span>
                    <span class="flex flex-1 flex-col gap-0.5"><span class="text-[15px] font-bold text-navy-950">{{ $title }}</span><span class="text-[13px] text-ink-600">{{ $sub }}</span></span>
                    <x-icon name="chevron" :size="18" class="text-ink-400 transition group-hover:translate-x-0.5 group-hover:text-brand-600" />
                </a>
            @endforeach
        </div>

        <div class="mt-auto flex flex-col gap-2">
            <a href="{{ $next[0][0] }}" class="btn-primary min-h-[54px] text-base">Commencer <x-icon name="arrow" :size="18" /></a>
            <a href="{{ route('dashboard') }}" class="btn-ghost">Aller à l’accueil</a>
        </div>
    </div>
</x-layouts.auth>
