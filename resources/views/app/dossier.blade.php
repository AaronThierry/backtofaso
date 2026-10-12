@php
    $docs = $dossier->documents;
    $counts = [
        'validated' => $docs->where('status', 'validated')->count(),
        'pending' => $docs->where('status', 'pending')->count(),
        'todo' => $docs->whereIn('status', ['missing', 'to_complete'])->count(),
    ];
    $docTone = [
        'validated' => 'bg-ok-50 text-ok-700',
        'pending' => 'bg-warn-50 text-warn-700',
        'to_complete' => 'bg-warn-50 text-warn-700',
        'missing' => 'bg-err-50 text-err-700',
    ];
@endphp
<x-layouts.app title="Mon dossier" active="dossier">
    <div class="flex flex-col gap-6 lg:gap-8">
        <x-page-header title="Mon dossier" :back="route('dashboard')" eyebrow="Suivi de candidature"
                       :subtitle="($dossier->program ?? 'Programme à définir avec votre conseiller').($dossier->university ? ' · '.$dossier->university : '')">
            @if ($dossier->advisor)
                <x-slot:actions>
                    <form method="POST" action="{{ route('chat.direct', $dossier->advisor) }}">@csrf
                        <button class="btn-primary min-h-11 px-4 text-sm"><x-icon name="chat" :size="18" /> <span class="hidden sm:inline">Écrire à mon conseiller</span><span class="sm:hidden">Conseiller</span></button>
                    </form>
                </x-slot:actions>
            @endif
        </x-page-header>

        <x-flash />
        @if ($errors->any())
            <div class="flex items-center gap-3 rounded-2xl bg-err-50 px-4 py-3.5 text-sm font-semibold text-err-700 ring-1 ring-err-700/15"><x-icon name="help" :size="18" /> {{ $errors->first() }}</div>
        @endif

        {{-- Récapitulatif --}}
        <section class="card flex animate-rise flex-col gap-5 p-5 sm:flex-row sm:items-center sm:p-6">
            <div class="flex items-center gap-5 sm:flex-1">
                <x-progress-ring :percent="$dossier->progressPercent()" :size="88" :stroke="8" />
                <div class="flex flex-col gap-1">
                    <span class="text-xs font-bold uppercase tracking-[0.14em] text-ink-500">Étape en cours</span>
                    <span class="font-display text-xl font-bold text-navy-950">{{ $dossier->stepLabel() }}</span>
                    <span class="text-[13.5px] text-ink-600">Étape {{ $dossier->current_step }} sur {{ count(\App\Models\Dossier::STEPS) }}</span>
                </div>
            </div>
            <div class="grid grid-cols-3 gap-2 sm:w-[360px]">
                @foreach ([['validated', 'Validés', 'text-ok-700 bg-ok-50'], ['pending', 'En vérification', 'text-warn-700 bg-warn-50'], ['todo', 'À fournir', 'text-err-700 bg-err-50']] as [$k, $label, $tone])
                    <div class="flex flex-col gap-0.5 rounded-xl px-3 py-2.5 {{ $tone }}">
                        <span class="font-display text-2xl font-extrabold leading-none">{{ $counts[$k] }}</span>
                        <span class="text-[11.5px] font-semibold leading-tight">{{ $label }}</span>
                    </div>
                @endforeach
            </div>
        </section>

        <div class="grid gap-6 lg:grid-cols-[minmax(0,5fr)_minmax(0,7fr)] lg:items-start lg:gap-8">
            {{-- Étapes --}}
            <section class="flex flex-col gap-3.5 lg:sticky lg:top-10">
                <h2 class="text-[20px] font-bold text-navy-950">Étapes</h2>
                <ol class="card p-5 lg:p-6">
                    @foreach ($dossier->stepsWithState() as $s)
                        <li class="flex gap-4">
                            <div class="flex w-8 flex-col items-center">
                                <span @class(['flex size-8 shrink-0 items-center justify-center rounded-full text-[13px] font-bold',
                                    'bg-brand-600 text-white' => $s['state'] === 'done',
                                    'bg-linear-to-br from-gold-400 to-gold-500 text-navy-950 ring-4 ring-gold-100' => $s['state'] === 'now',
                                    'bg-white text-ink-500 ring-2 ring-ink-200' => $s['state'] === 'todo'])>
                                    @if ($s['state'] === 'done')
                                        <x-icon name="check" :size="15" stroke-width="3" />
                                    @else
                                        {{ $s['n'] }}
                                    @endif
                                </span>
                                @unless ($loop->last)
                                    <span @class(['my-1 min-h-5 w-0.5 flex-1 rounded-full', 'bg-brand-600' => $s['state'] === 'done', 'bg-ink-100' => $s['state'] !== 'done'])></span>
                                @endunless
                            </div>
                            <div @class(['mb-4 flex flex-1 flex-col gap-0.5 rounded-xl', 'bg-gold-50 px-3.5 py-2.5 ring-1 ring-gold-200' => $s['state'] === 'now', 'pt-1' => $s['state'] !== 'now'])>
                                <span @class(['text-[15px]', 'font-bold text-navy-950' => $s['state'] !== 'todo', 'font-semibold text-ink-500' => $s['state'] === 'todo'])>{{ $s['title'] }}</span>
                                <span class="text-[13px] text-ink-600">{{ $s['sub'] }}</span>
                                @if ($s['state'] === 'now')
                                    <span class="mt-1 text-xs font-bold text-gold-700">En cours</span>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ol>
            </section>

            {{-- Documents --}}
            <section class="flex flex-col gap-3.5">
                <div class="flex items-baseline justify-between">
                    <h2 class="text-[20px] font-bold text-navy-950">Documents</h2>
                    <span class="text-[13px] font-semibold text-ink-500">{{ $counts['validated'] }} sur {{ $docs->count() }} validés</span>
                </div>
                <div class="card divide-y divide-ink-100 overflow-hidden">
                    @forelse ($docs as $doc)
                        <div class="flex flex-col gap-2 px-4 py-4 transition hover:bg-ink-50/50 sm:px-5" x-data="{ uploading: false }">
                            <div class="flex items-center gap-3.5">
                                <span class="flex size-11 shrink-0 items-center justify-center rounded-xl {{ $docTone[$doc->status] ?? 'bg-ink-50 text-ink-600' }}">
                                    <x-icon :name="$doc->status === 'validated' ? 'check' : 'file'" :size="20" :stroke-width="$doc->status === 'validated' ? 2.6 : 1.8" />
                                </span>
                                <span class="flex min-w-0 flex-1 flex-col gap-1">
                                    <span class="text-[15px] font-semibold text-navy-950">{{ $doc->label }}</span>
                                    <span class="{{ $doc->statusClass() }} self-start">{{ $doc->statusLabel() }}</span>
                                </span>
                                @if ($doc->canUpload())
                                    <form method="POST" action="{{ route('dossier.upload', $doc) }}" enctype="multipart/form-data">
                                        @csrf
                                        <label @class(['inline-flex min-h-10 cursor-pointer items-center gap-1.5 rounded-xl px-3.5 text-[13.5px] font-bold transition', 'bg-brand-600 text-white shadow-glow hover:bg-brand-700' => $doc->status === 'missing', 'bg-white text-ink-800 ring-1 ring-ink-200 hover:bg-ink-50' => $doc->status !== 'missing'])>
                                            <span x-show="!uploading" class="flex items-center gap-1.5"><x-icon name="upload" :size="16" /> {{ $doc->status === 'missing' ? 'Ajouter' : 'Remplacer' }}</span>
                                            <span x-show="uploading" x-cloak class="flex items-center gap-1.5"><span class="size-3.5 animate-spin rounded-full border-2 border-current/30 border-t-current"></span> Envoi…</span>
                                            <input type="file" name="file" accept=".pdf,image/*" class="sr-only" @change="uploading = true; $el.form.submit()">
                                        </label>
                                    </form>
                                @endif
                            </div>
                            @if ($doc->note)
                                <span class="ml-[58px] rounded-lg bg-warn-50 px-3 py-2 text-[13px] text-warn-700">{{ $doc->note }}</span>
                            @endif
                        </div>
                    @empty
                        <x-empty icon="file" title="Aucun document demandé" text="Votre conseiller vous indiquera les pièces à fournir." class="m-4 border-0" />
                    @endforelse
                </div>
                <p class="flex items-start gap-2 text-[12.5px] leading-relaxed text-ink-500"><x-icon name="shield" :size="16" class="mt-px shrink-0" /> Formats acceptés : PDF, JPG ou PNG, 8 Mo maximum. Vos documents sont stockés de façon privée, ils ne sont pas publiés sur le site. Sur téléphone, vous pouvez photographier le document.</p>
            </section>
        </div>
    </div>
</x-layouts.app>
