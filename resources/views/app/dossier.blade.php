<x-layouts.app title="Mon dossier" active="dossier">
    <div class="flex flex-col gap-[18px] lg:gap-7">
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard') }}" aria-label="Retour" class="flex size-11 shrink-0 items-center justify-center rounded-full border border-ink-200 bg-white lg:hidden"><x-icon name="back" /></a>
            <div class="flex min-w-0 flex-1 flex-col gap-0.5">
                <h1 class="text-2xl font-bold lg:text-[28px]">Mon dossier</h1>
                <span class="truncate text-[12.5px] text-ink-600 lg:text-[15px]">{{ $dossier->program ?? 'Programme à définir avec votre conseiller' }}{{ $dossier->university ? ' · '.$dossier->university : '' }}</span>
            </div>
            @if ($dossier->advisor)
                <form method="POST" action="{{ route('chat.direct', $dossier->advisor) }}">@csrf
                    <button aria-label="Écrire à mon conseiller" class="flex size-11 items-center justify-center rounded-full bg-brand-600 text-white hover:bg-brand-700 lg:w-auto lg:gap-2 lg:rounded-[10px] lg:px-4 lg:text-[15px] lg:font-bold">
                        <x-icon name="chat" /> <span class="hidden lg:inline">Écrire à mon conseiller</span>
                    </button>
                </form>
            @endif
        </div>

        <x-flash />
        @if ($errors->any())
            <div class="rounded-[10px] bg-err-50 px-4 py-3 text-sm font-semibold text-err-700">{{ $errors->first() }}</div>
        @endif

        <div class="grid gap-[18px] lg:grid-cols-[minmax(0,5fr)_minmax(0,7fr)] lg:items-start lg:gap-8">
            {{-- Étapes --}}
            <section class="flex flex-col gap-2.5 lg:sticky lg:top-10">
                <div class="hidden items-baseline justify-between lg:flex">
                    <h2 class="text-[19px] font-semibold">Étapes</h2>
                    <span class="text-[13px] text-ink-600">{{ $dossier->progressPercent() }} % accompli</span>
                </div>
                <div class="card p-[18px] lg:p-6">
                    @foreach ($dossier->stepsWithState() as $s)
                        <div class="flex gap-3.5">
                            <div class="flex w-[26px] flex-col items-center">
                                <span @class(['flex size-[26px] items-center justify-center rounded-full text-xs font-bold',
                                    'bg-brand-600 text-white' => $s['state'] === 'done',
                                    'bg-gold-600 text-white' => $s['state'] === 'now',
                                    'border-2 border-ink-300 bg-white text-ink-600' => $s['state'] === 'todo'])>
                                    @if ($s['state'] === 'done')
                                        <x-icon name="check" :size="14" stroke-width="3" />
                                    @else
                                        {{ $s['n'] }}
                                    @endif
                                </span>
                                @unless ($loop->last)
                                    <span @class(['min-h-3.5 w-0.5 flex-1', 'bg-brand-600' => $s['state'] === 'done', 'bg-ink-200' => $s['state'] !== 'done'])></span>
                                @endunless
                            </div>
                            <div class="flex flex-col gap-0.5 pb-3.5 pt-0.5">
                                <span @class(['text-[14.5px]', 'font-bold' => $s['state'] !== 'todo', 'font-semibold text-ink-600' => $s['state'] === 'todo'])>{{ $s['title'] }}</span>
                                <span class="text-[12.5px] text-ink-600">{{ $s['sub'] }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            {{-- Documents --}}
            <section class="flex flex-col gap-2.5">
                <div class="flex items-baseline justify-between">
                    <h2 class="text-[19px] font-semibold">Documents</h2>
                    <span class="text-[13px] text-ink-600">{{ $dossier->documents->where('status', 'validated')->count() }} sur {{ $dossier->documents->count() }} validés</span>
                </div>
                <div class="flex flex-col gap-2.5 lg:gap-0 lg:divide-y lg:divide-ink-100 lg:overflow-hidden lg:rounded-xl lg:border lg:border-ink-200 lg:bg-white">
                    @foreach ($dossier->documents as $doc)
                        <div class="card flex flex-col gap-2 px-3.5 py-3 lg:rounded-none lg:border-0 lg:bg-white lg:px-5 lg:py-4" x-data>
                            <div class="flex min-h-7 flex-wrap items-center gap-3">
                                <x-icon name="file" :size="22" class="text-ink-600" />
                                <span class="min-w-0 flex-1 text-sm font-semibold lg:text-[15px]">{{ $doc->label }}</span>
                                <span class="{{ $doc->statusClass() }}">{{ $doc->statusLabel() }}</span>
                                @if ($doc->canUpload())
                                    <form method="POST" action="{{ route('dossier.upload', $doc) }}" enctype="multipart/form-data">
                                        @csrf
                                        <label class="inline-flex min-h-9 cursor-pointer items-center gap-1.5 rounded-[10px] bg-brand-600 px-3 text-[13px] font-bold text-white hover:bg-brand-700">
                                            <x-icon name="upload" :size="16" class="hidden lg:block" />
                                            {{ $doc->status === 'missing' ? 'Ajouter' : 'Remplacer' }}
                                            <input type="file" name="file" accept=".pdf,image/*" class="sr-only" @change="$el.form.submit()">
                                        </label>
                                    </form>
                                @endif
                            </div>
                            @if ($doc->note)
                                <span class="pl-[34px] text-[12.5px] text-warn-700">{{ $doc->note }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
                <p class="text-xs text-ink-600">Formats acceptés : PDF, JPG ou PNG, 8 Mo maximum. Sur téléphone, vous pouvez photographier le document.</p>
            </section>
        </div>
    </div>
</x-layouts.app>
