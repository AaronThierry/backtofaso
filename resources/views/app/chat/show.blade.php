@php
    $topicIcons = ['annonces' => 'bell', 'entraide' => 'help', 'emplois' => 'briefcase', 'bourses' => 'graduation'];
    $isDirect = $conversation->type === \App\Models\Conversation::DIRECT;
    $other = $isDirect ? $conversation->users->firstWhere('id', '!=', $user->id) : null;
    $subtitle = match (true) {
        $conversation->isSuper() => 'Super groupe · '.$membersCount.' membre'.($membersCount > 1 ? 's' : ''),
        $isDirect => $other?->role_label ?? 'Conversation privée',
        default => 'Groupe '.($conversation->is_public ? 'public' : 'privé').' · '.$membersCount.' membre'.($membersCount > 1 ? 's' : ''),
    };
@endphp
<x-layouts.app :title="$conversation->displayNameFor($user)" active="chat" :bare="true">
    <div class="lg:grid lg:h-dvh lg:grid-cols-[minmax(320px,390px)_minmax(0,1fr)]">
        {{-- Ordinateur : liste des conversations à gauche --}}
        <div class="scrollbar-thin hidden overflow-y-auto border-r border-ink-200/70 bg-white p-5 lg:block">
            @include('app.chat._list', $list + ['activeId' => $conversation->id])
        </div>

        <div class="relative flex h-dvh min-w-0 flex-col bg-canvas"
             x-data="chatRoom({
                fetchUrl: @js(route('chat.messages', $conversation)),
                postUrl: @js(route('chat.store', $conversation)),
                deleteUrl: @js(route('chat.destroy', [$conversation, '__ID__'])),
                topic: @js($topic),
                initial: @js($initialMessages),
                seenUntil: @js($seenUntil),
                hasMore: @js($hasMore),
                direct: @js($isDirect),
             })"
             @dragover.prevent="dragging = true" @dragleave.self.prevent="dragging = false" @drop.prevent="dropFile($event)"
             @keydown.escape.window="lightbox = null; info = false">

            {{-- En-tête --}}
            <header class="z-10 flex flex-col gap-3 border-b border-ink-200/70 bg-white/90 px-3 pb-3 pt-[calc(env(safe-area-inset-top)+10px)] backdrop-blur-lg lg:px-6 lg:pt-4">
                <div class="flex items-center gap-3">
                    <a href="{{ route('chat.index') }}" aria-label="Retour" class="flex size-10 items-center justify-center rounded-full text-ink-700 hover:bg-ink-50 lg:hidden"><x-icon name="back" :size="22" /></a>
                    <button type="button" @click="info = true" class="flex min-w-0 flex-1 items-center gap-3 rounded-xl text-left transition hover:opacity-80" aria-label="Informations sur la conversation">
                        @if ($conversation->isSuper())
                            <x-logo :size="44" />
                        @elseif ($isDirect && $other)
                            <x-avatar :user="$other" :size="44" />
                        @elseif ($conversation->avatarUrl())
                            <img src="{{ $conversation->avatarUrl() }}" alt="" class="size-11 shrink-0 rounded-[30%] object-cover ring-1 ring-ink-900/5">
                        @else
                            <x-avatar :initials="$conversation->initials($user)" :size="44" tone="ink" square />
                        @endif
                        <span class="flex min-w-0 flex-1 flex-col gap-0.5">
                            <span class="truncate font-display text-[17px] font-bold text-navy-950">{{ $conversation->displayNameFor($user) }}</span>
                            <span class="flex items-center gap-1.5 truncate text-xs text-ink-500"><span class="size-1.5 shrink-0 rounded-full bg-ok-500"></span>{{ $subtitle }}</span>
                        </span>
                    </button>
                    <button type="button" @click="info = true" class="icon-btn size-10 shadow-none" aria-label="Informations" title="Informations"><x-icon name="info" :size="19" /></button>
                </div>
                @if ($conversation->isSuper())
                    <nav class="-mx-1 flex gap-2 overflow-x-auto px-1 pb-0.5" style="scrollbar-width: none" aria-label="Thèmes">
                        @foreach (\App\Models\Conversation::TOPICS as $key => [$label, $restricted])
                            <a href="{{ route('chat.show', [$conversation, 'theme' => $key]) }}"
                               @class(['inline-flex min-h-9 shrink-0 items-center gap-1.5 rounded-full px-3.5 text-[13px] font-semibold transition', 'bg-navy-900 text-white shadow-sm' => $topic === $key, 'bg-white text-ink-700 ring-1 ring-ink-200 hover:ring-brand-300' => $topic !== $key])>
                                <x-icon :name="$topicIcons[$key] ?? 'chat'" :size="15" /> {{ $label }}
                                @if ($restricted)<x-icon name="lock" :size="12" class="opacity-60" />@endif
                            </a>
                        @endforeach
                    </nav>
                @endif
            </header>

            @if ($pinned)
                <div class="flex items-center gap-2.5 border-b border-gold-200 bg-gold-50 px-4 py-2.5 text-[13px] font-semibold text-gold-700 lg:px-6">
                    <span class="flex size-6 shrink-0 items-center justify-center rounded-full bg-gold-400 text-navy-950"><x-icon name="pin" :size="13" /></span>
                    <span class="truncate">{{ $pinned->body }}</span>
                </div>
            @endif

            {{-- Messages --}}
            <div x-ref="list" @scroll.passive="onScroll()" class="bg-dots scrollbar-thin relative flex flex-1 flex-col overflow-y-auto px-3 py-5 lg:px-8 lg:py-7">
                <div x-show="hasMore" class="flex justify-center pb-4">
                    <button type="button" @click="loadOlder()" :disabled="loadingOlder" class="inline-flex items-center gap-2 rounded-full bg-white px-4 py-2 text-[13px] font-semibold text-ink-700 shadow-card ring-1 ring-ink-900/5 transition hover:text-navy-950 disabled:opacity-60">
                        <span x-show="loadingOlder" class="size-3.5 animate-spin rounded-full border-2 border-ink-200 border-t-ink-600"></span>
                        Messages précédents
                    </button>
                </div>

                <template x-if="messages.length === 0">
                    <div class="m-auto flex flex-col items-center gap-3 text-center">
                        <span class="flex size-16 items-center justify-center rounded-2xl bg-white text-brand-600 shadow-card"><x-icon name="chat" :size="28" /></span>
                        <span class="font-display text-lg font-bold text-navy-950">Aucun message pour l’instant</span>
                        <span class="max-w-xs text-sm text-ink-600">{{ $canPost ? 'Lancez la conversation, votre message sera le premier.' : 'Les publications de l’équipe apparaîtront ici.' }}</span>
                    </div>
                </template>

                <template x-for="(m, i) in messages" :key="m.id">
                    <div class="flex flex-col">
                        {{-- Séparateur de date --}}
                        <template x-if="newDay(i)">
                            <div class="my-4 flex justify-center">
                                <span class="rounded-full bg-white/90 px-3 py-1 text-[11.5px] font-bold text-ink-600 shadow-xs ring-1 ring-ink-900/5 backdrop-blur" x-text="m.dayLabel"></span>
                            </div>
                        </template>

                        <div class="group/msg flex items-end gap-2" :class="[m.mine && 'justify-end', startsGroup(i) ? 'mt-3' : 'mt-0.5']">
                            <template x-if="!m.mine">
                                <span class="size-8 shrink-0" :class="!endsGroup(i) && 'invisible'">
                                    <template x-if="m.avatar"><img :src="m.avatar" alt="" class="size-8 rounded-full object-cover ring-1 ring-ink-900/5"></template>
                                    <template x-if="!m.avatar"><span class="flex size-8 items-center justify-center rounded-full bg-linear-to-br from-brand-100 to-brand-200 text-[11px] font-bold text-brand-800" x-text="m.initials"></span></template>
                                </span>
                            </template>

                            {{-- Supprimer (au survol, ses propres messages) --}}
                            <template x-if="m.mine && m.canDelete && !m.pending">
                                <button type="button" @click="remove(m)" class="mb-2 flex size-7 shrink-0 items-center justify-center rounded-full text-ink-400 opacity-0 transition hover:bg-err-50 hover:text-err-700 focus:opacity-100 group-hover/msg:opacity-100" aria-label="Supprimer le message" title="Supprimer"><x-icon name="trash" :size="15" /></button>
                            </template>

                            <div class="flex max-w-[82%] flex-col gap-1.5 lg:max-w-[64%] xl:max-w-[54%]"
                                 :class="[m.mine ? 'rounded-[20px] bg-linear-to-br from-brand-500 to-brand-600 text-white shadow-[0_6px_16px_-8px_rgb(0_113_227/0.6)]' : 'rounded-[20px] bg-white text-ink-900 shadow-card ring-1 ring-ink-900/[0.04]',
                                          endsGroup(i) && (m.mine ? 'rounded-br-md' : 'rounded-bl-md'), m.pending && 'opacity-70', m.failed && 'ring-2 ring-err-500',
                                          m.file?.image && !m.body ? 'p-1' : 'px-3.5 py-2.5']">
                                <template x-if="!m.mine && startsGroup(i) && !direct">
                                    <span class="flex items-center gap-1.5" :class="m.file?.image && !m.body && 'px-2.5 pt-1.5'">
                                        <span class="text-[12.5px] font-bold text-brand-700" x-text="m.author"></span>
                                        <span x-show="m.official" class="badge-gold px-1.5 py-0 text-[10px]">Officiel</span>
                                    </span>
                                </template>

                                {{-- Image : affichée dans la conversation --}}
                                <template x-if="m.file && m.file.image">
                                    <button type="button" @click="m.file.url && (lightbox = m.file.url)" class="block overflow-hidden rounded-2xl" :class="m.body && '-mx-1.5 -mt-0.5'">
                                        <img :src="m.file.url || m.file.preview" :alt="m.file.name" loading="lazy" class="max-h-80 w-full max-w-sm object-cover transition hover:opacity-95">
                                    </button>
                                </template>

                                {{-- Autre fichier --}}
                                <template x-if="m.file && !m.file.image">
                                    <a :href="m.file.url || '#'" target="_blank" rel="noopener" class="flex items-center gap-3 rounded-xl p-2.5 transition" :class="m.mine ? 'bg-white/15 hover:bg-white/25' : 'bg-ink-50 hover:bg-ink-100'">
                                        <span class="flex size-10 shrink-0 items-center justify-center rounded-lg" :class="m.mine ? 'bg-white text-brand-600' : 'bg-err-50 text-err-700'"><x-icon name="file" :size="19" /></span>
                                        <span class="flex min-w-0 flex-col"><span class="truncate text-[13px] font-bold" x-text="m.file.name"></span><span class="text-[11.5px] opacity-75" x-text="m.file.size || 'Fichier'"></span></span>
                                    </a>
                                </template>

                                <span x-show="m.body" class="whitespace-pre-line break-words text-[14.5px] leading-snug [&_a]:font-semibold [&_a]:underline [&_a]:underline-offset-2" x-html="render(m.body)"></span>

                                <span class="flex items-center justify-end gap-1.5 text-[10.5px] font-medium tabular-nums" :class="[m.mine ? 'text-white/75' : 'text-ink-500', m.file?.image && !m.body && 'px-2 pb-1']">
                                    <template x-if="m.pending"><span class="flex items-center gap-1"><span class="size-2.5 animate-spin rounded-full border border-current border-t-transparent"></span> Envoi…</span></template>
                                    <template x-if="m.failed"><button type="button" @click="retry(m)" class="font-bold text-white underline">Échec, réessayer</button></template>
                                    <span x-show="!m.pending && !m.failed" x-text="m.time"></span>
                                </span>
                            </div>
                        </div>

                        {{-- « Vu » sous le dernier message lu par l'autre personne --}}
                        <template x-if="direct && m.id === lastSeenId">
                            <span class="mt-1 flex items-center justify-end gap-1 pr-1 text-[11px] font-semibold text-ink-500"><x-icon name="eye" :size="13" /> Vu</span>
                        </template>
                    </div>
                </template>
            </div>

            {{-- Revenir en bas --}}
            <button type="button" x-show="!atBottom" x-cloak x-transition @click="scroll(true)"
                    class="absolute bottom-28 right-5 z-10 flex items-center gap-2 rounded-full bg-navy-900 px-3.5 py-2.5 text-[13px] font-bold text-white shadow-lift transition hover:bg-navy-800 lg:right-8">
                <x-icon name="down" :size="16" stroke-width="2.4" />
                <span x-text="newCount ? newCount + ' nouveau' + (newCount > 1 ? 'x' : '') : 'Derniers messages'"></span>
            </button>

            {{-- Saisie --}}
            @if ($canPost)
                <form @submit.prevent="send" class="flex flex-col gap-2 border-t border-ink-200/70 bg-white px-3 pb-[calc(env(safe-area-inset-bottom)+12px)] pt-3 lg:px-6 lg:pb-5">
                    <div x-show="error" x-cloak x-transition class="flex items-center gap-2 rounded-xl bg-err-50 px-3 py-2 text-[13px] font-semibold text-err-700">
                        <x-icon name="help" :size="16" /> <span x-text="error"></span>
                    </div>
                    <div x-show="fileName" x-cloak class="flex items-center gap-3 self-start rounded-2xl bg-brand-50 p-1.5 pr-2 ring-1 ring-brand-100">
                        <template x-if="filePreview"><img :src="filePreview" alt="" class="size-12 rounded-xl object-cover"></template>
                        <template x-if="!filePreview"><span class="flex size-12 items-center justify-center rounded-xl bg-white text-brand-600"><x-icon name="file" :size="20" /></span></template>
                        <span class="flex max-w-[220px] flex-col"><span class="truncate text-[13px] font-semibold text-brand-800" x-text="fileName"></span><span class="text-[11.5px] text-brand-700/70">Prêt à envoyer</span></span>
                        <button type="button" @click="clearFile()" aria-label="Retirer le fichier" class="flex size-7 items-center justify-center rounded-lg text-brand-700 hover:bg-brand-100"><x-icon name="close" :size="15" /></button>
                    </div>
                    <div class="flex items-end gap-2">
                        <label class="flex size-11 shrink-0 cursor-pointer items-center justify-center rounded-full text-ink-600 transition hover:bg-ink-100 hover:text-ink-900" aria-label="Joindre un fichier ou une photo" title="Joindre un fichier ou une photo">
                            <x-icon name="plus" :size="22" stroke-width="2" />
                            <input type="file" x-ref="file" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx" class="sr-only" @change="pickFile($event.target.files[0])">
                        </label>
                        <label class="flex min-h-11 flex-1 items-center rounded-[22px] bg-ink-50 px-4 py-2 ring-1 ring-ink-200/70 transition focus-within:bg-white focus-within:ring-2 focus-within:ring-brand-300">
                            <span class="sr-only">Votre message</span>
                            <textarea x-ref="input" x-model="body" rows="1" maxlength="4000"
                                      @input="grow()" @keydown.enter="if (!$event.shiftKey) { $event.preventDefault(); send(); }"
                                      @paste="pasteImage($event)"
                                      placeholder="{{ 'Écrire'.($topic ? ' dans '.\App\Models\Conversation::TOPICS[$topic][0] : ($isDirect && $other ? ' à '.$other->first_name : '')).'…' }}"
                                      class="max-h-36 w-full resize-none bg-transparent text-[15px] leading-snug outline-none placeholder:text-ink-400"></textarea>
                        </label>
                        <button type="submit" aria-label="Envoyer" :disabled="!body.trim() && !fileName"
                                class="flex size-11 shrink-0 items-center justify-center rounded-full bg-linear-to-br from-brand-500 to-brand-600 text-white shadow-glow transition hover:scale-105 disabled:scale-100 disabled:opacity-40 disabled:shadow-none">
                            <x-icon name="send" :size="19" stroke-width="2.2" />
                        </button>
                    </div>
                    <span class="hidden px-14 text-[11px] text-ink-400 lg:block">Entrée pour envoyer · Maj + Entrée pour aller à la ligne · Glissez une photo ou un fichier ici</span>
                </form>
            @else
                <div class="flex items-center justify-center gap-2 border-t border-ink-200/70 bg-white px-4 pb-[calc(env(safe-area-inset-bottom)+18px)] pt-4 text-[13.5px] font-semibold text-ink-500">
                    <x-icon name="lock" :size="16" /> Seule l’équipe Travel Express publie dans ce thème
                </div>
            @endif

            {{-- Confirmation (ajout de membres, etc.) --}}
            @if (session('status'))
                <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-transition
                     class="absolute left-1/2 top-24 z-20 flex -translate-x-1/2 items-center gap-2.5 rounded-full bg-navy-900 py-2.5 pl-3 pr-4 text-[13.5px] font-semibold text-white shadow-lift">
                    <span class="flex size-5 items-center justify-center rounded-full bg-ok-500"><x-icon name="check" :size="12" stroke-width="3" /></span> {{ session('status') }}
                </div>
            @endif

            {{-- Dépôt de fichier --}}
            <div x-show="dragging" x-cloak x-transition.opacity class="pointer-events-none absolute inset-3 z-20 flex flex-col items-center justify-center gap-3 rounded-3xl border-2 border-dashed border-brand-400 bg-brand-50/90 text-brand-700 backdrop-blur-sm">
                <x-icon name="upload" :size="34" />
                <span class="font-display text-lg font-bold">Déposez votre fichier ici</span>
            </div>

            {{-- Image en grand --}}
            <template x-teleport="body">
                <div x-show="lightbox" x-cloak x-transition.opacity @click="lightbox = null" class="fixed inset-0 z-[70] flex items-center justify-center bg-navy-950/90 p-5 backdrop-blur-sm">
                    <img :src="lightbox" alt="" class="max-h-[88dvh] max-w-full rounded-2xl object-contain shadow-2xl">
                    <div class="absolute right-5 top-5 flex gap-2">
                        <a :href="lightbox" download target="_blank" @click.stop class="flex size-11 items-center justify-center rounded-full bg-white/15 text-white hover:bg-white/25" aria-label="Télécharger"><x-icon name="upload" :size="19" class="rotate-180" /></a>
                        <button type="button" class="flex size-11 items-center justify-center rounded-full bg-white/15 text-white hover:bg-white/25" aria-label="Fermer"><x-icon name="close" /></button>
                    </div>
                </div>
            </template>

            {{-- Panneau d'informations --}}
            <template x-teleport="body">
                <div x-show="info" x-cloak class="fixed inset-0 z-[60] flex justify-end">
                    <div x-show="info" x-transition.opacity @click="info = false" class="absolute inset-0 bg-navy-950/40 backdrop-blur-[2px]"></div>
                    <aside x-show="info" x-transition:enter="transition duration-300 ease-out" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                           x-transition:leave="transition duration-200 ease-in" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
                           class="relative flex h-full w-full max-w-[380px] flex-col overflow-y-auto bg-white shadow-2xl" aria-label="Informations sur la conversation">
                        <div class="bg-hero relative flex flex-col items-center gap-3 px-6 pb-6 pt-[calc(env(safe-area-inset-top)+20px)] text-center text-white">
                            <div class="bg-weave pointer-events-none absolute inset-0 opacity-40"></div>
                            <button type="button" @click="info = false" class="absolute right-4 top-[calc(env(safe-area-inset-top)+12px)] flex size-10 items-center justify-center rounded-full bg-white/10 hover:bg-white/20" aria-label="Fermer"><x-icon name="close" :size="19" /></button>
                            <span class="relative mt-4">
                                @if ($conversation->isSuper())
                                    <x-logo :size="80" />
                                @elseif ($isDirect && $other)
                                    <x-avatar :user="$other" :size="88" class="ring-4 ring-white/20" />
                                @elseif ($conversation->avatarUrl())
                                    <img src="{{ $conversation->avatarUrl() }}" alt="" class="size-20 rounded-[30%] object-cover ring-4 ring-white/20">
                                @else
                                    <x-avatar :initials="$conversation->initials($user)" :size="80" tone="gold" square />
                                @endif
                            </span>
                            <span class="relative font-display text-[22px] font-bold leading-tight">{{ $conversation->displayNameFor($user) }}</span>
                            <span class="relative text-[13.5px] text-white/65">{{ $subtitle }}</span>
                            @if ($conversation->description && ! $isDirect)
                                <p class="relative text-[14px] leading-relaxed text-white/80">{{ $conversation->description }}</p>
                            @endif
                        </div>

                        <div class="flex flex-1 flex-col gap-5 p-5">
                            @if ($isDirect && $other)
                                <div class="flex flex-col gap-2">
                                    @if ($other->talentProfile && ($other->talentProfile->verified_at || $user->isStaff()))
                                        <a href="{{ route('talents.show', $other->talentProfile) }}" class="btn-secondary min-h-11 text-sm"><x-icon name="briefcase" :size="17" /> Voir son profil talent</a>
                                    @endif
                                    <p class="rounded-xl bg-ink-50 px-4 py-3 text-[13px] leading-relaxed text-ink-600">Conversation privée : seuls vous et {{ $other->first_name }} voyez ces messages.</p>
                                </div>
                            @elseif ($conversation->isSuper())
                                <div class="flex flex-col gap-3">
                                    <span class="text-xs font-bold uppercase tracking-[0.14em] text-ink-500">Thèmes</span>
                                    @foreach (\App\Models\Conversation::TOPICS as $key => [$label, $restricted])
                                        <a href="{{ route('chat.show', [$conversation, 'theme' => $key]) }}" class="flex items-center gap-3 rounded-xl px-2 py-2 transition hover:bg-ink-50">
                                            <span class="flex size-9 items-center justify-center rounded-lg bg-brand-50 text-brand-600"><x-icon :name="$topicIcons[$key] ?? 'chat'" :size="17" /></span>
                                            <span class="flex flex-1 flex-col"><span class="text-sm font-bold text-navy-950">{{ $label }}</span><span class="text-xs text-ink-500">{{ $restricted ? 'Publié par l’équipe Travel Express' : 'Ouvert à tous les membres' }}</span></span>
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                {{-- Inviter des membres --}}
                                @if ($canManage)
                                    <div x-data="groupInvite(@js(route('chat.members')), @js($members->pluck('id')->values()))" class="flex flex-col gap-2">
                                        <button type="button" x-show="!open" @click="start()" class="btn-primary min-h-11 text-sm"><x-icon name="plus" :size="18" stroke-width="2.4" /> Ajouter des membres</button>
                                        <form x-show="open" x-cloak method="POST" action="{{ route('groups.members.add', $conversation) }}" class="flex flex-col gap-3 rounded-2xl bg-ink-50 p-3 ring-1 ring-ink-100">
                                            @csrf
                                            <div class="flex items-center justify-between">
                                                <span class="text-sm font-bold text-navy-950">Ajouter des membres</span>
                                                <button type="button" @click="open = false" class="text-xs font-semibold text-ink-500 hover:text-ink-900">Annuler</button>
                                            </div>
                                            <label class="field-group min-h-10 bg-white">
                                                <x-icon name="search" :size="16" class="ml-3 text-ink-400" />
                                                <span class="sr-only">Rechercher un membre</span>
                                                <input x-ref="search" x-model="term" @input.debounce.250ms="search()" type="search" placeholder="Nom d’un membre…" class="min-w-0 flex-1 bg-transparent px-2.5 text-sm outline-none placeholder:text-ink-400">
                                            </label>
                                            <div class="flex max-h-60 flex-col gap-0.5 overflow-y-auto">
                                                <template x-if="loading"><p class="px-2 py-3 text-center text-xs text-ink-500">Recherche…</p></template>
                                                <template x-if="!loading && available.length === 0"><p class="px-2 py-3 text-center text-xs text-ink-500">Aucune personne à ajouter.</p></template>
                                                <template x-for="p in available" :key="p.id">
                                                    <label class="flex cursor-pointer items-center gap-3 rounded-xl px-2 py-2 transition" :class="picked.includes(p.id) ? 'bg-brand-50' : 'hover:bg-white'">
                                                        <template x-if="p.avatar"><img :src="p.avatar" alt="" class="size-9 rounded-full object-cover"></template>
                                                        <template x-if="!p.avatar"><span class="flex size-9 items-center justify-center rounded-full bg-linear-to-br from-brand-100 to-brand-200 text-[11px] font-bold text-brand-800" x-text="p.initials"></span></template>
                                                        <span class="flex min-w-0 flex-1 flex-col"><span class="truncate text-[13.5px] font-bold text-navy-950" x-text="p.name"></span><span class="truncate text-[11.5px] text-ink-500" x-text="p.role"></span></span>
                                                        <input type="checkbox" name="members[]" :value="p.id" x-model.number="picked" class="peer sr-only">
                                                        <span class="flex size-5 shrink-0 items-center justify-center rounded-full ring-2 transition" :class="picked.includes(p.id) ? 'bg-brand-600 text-white ring-brand-600' : 'text-transparent ring-ink-300'"><x-icon name="check" :size="11" stroke-width="3.4" /></span>
                                                    </label>
                                                </template>
                                            </div>
                                            {{-- Personnes cochées mais masquées par une nouvelle recherche : toujours envoyées --}}
                                            <template x-for="id in picked.filter(id => !available.some(p => p.id === id))" :key="'h' + id">
                                                <input type="hidden" name="members[]" :value="id">
                                            </template>
                                            <button class="btn-primary min-h-10 text-sm" :disabled="!picked.length" x-text="picked.length ? 'Ajouter ' + picked.length + ' personne' + (picked.length > 1 ? 's' : '') : 'Choisissez des personnes'"></button>
                                        </form>
                                    </div>
                                @endif

                                <div class="flex flex-col gap-2">
                                    <span class="flex items-center justify-between text-xs font-bold uppercase tracking-[0.14em] text-ink-500">Membres <span class="normal-case tracking-normal">{{ $members->count() }}</span></span>
                                    @foreach ($members as $member)
                                        <div class="group/member flex items-center gap-3 rounded-xl px-1.5 py-2">
                                            <x-avatar :user="$member" :size="40" />
                                            <span class="flex min-w-0 flex-1 flex-col">
                                                <span class="truncate text-sm font-bold text-navy-950">{{ $member->name }}{{ $member->id === $user->id ? ' (vous)' : '' }}</span>
                                                <span class="truncate text-xs text-ink-500">{{ $member->role_label }}</span>
                                            </span>
                                            @if ($member->pivot->role === 'admin')
                                                <span class="rounded-full bg-gold-50 px-2 py-0.5 text-[11px] font-bold text-gold-700 ring-1 ring-gold-200">Admin</span>
                                            @endif
                                            @if ($member->id !== $user->id)
                                                <form method="POST" action="{{ route('chat.direct', $member) }}">@csrf
                                                    <button class="flex size-8 items-center justify-center rounded-lg text-ink-400 hover:bg-brand-50 hover:text-brand-600" aria-label="Écrire à {{ $member->first_name }}" title="Écrire en privé"><x-icon name="chat" :size="16" /></button>
                                                </form>
                                                @if ($canManage)
                                                    <form method="POST" action="{{ route('groups.members.remove', [$conversation, $member]) }}" onsubmit="return confirm(@js('Retirer '.$member->first_name.' du groupe ?'))">@csrf @method('DELETE')
                                                        <button class="flex size-8 items-center justify-center rounded-lg text-ink-400 hover:bg-err-50 hover:text-err-700" aria-label="Retirer {{ $member->first_name }} du groupe" title="Retirer du groupe"><x-icon name="close" :size="16" /></button>
                                                    </form>
                                                @endif
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                                <form method="POST" action="{{ route('groups.leave', $conversation) }}" class="mt-auto" onsubmit="return confirm('Quitter ce groupe ? Vous ne verrez plus ses messages.')">@csrf
                                    <button class="btn w-full bg-err-50 text-err-700 hover:bg-err-500 hover:text-white"><x-icon name="logout" :size="18" /> Quitter le groupe</button>
                                </form>
                            @endif
                        </div>
                    </aside>
                </div>
            </template>
        </div>
    </div>

    @push('scripts')
        <script>
            function chatRoom({ fetchUrl, postUrl, deleteUrl, topic, initial, seenUntil, hasMore, direct }) {
                // Fichiers en attente d'envoi, gardés hors de l'état réactif d'Alpine (FormData exige le vrai objet File)
                const pendingFiles = new Map();
                const today = () => { const d = new Date(); return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0'); };
                return {
                    messages: initial, seenUntil, hasMore, direct,
                    body: '', fileName: '', filePreview: null, error: '',
                    atBottom: true, newCount: 0, loadingOlder: false, dragging: false, lightbox: null, info: false,
                    init() {
                        this.scroll(true, 'auto');
                        // Rafraîchissement toutes les 5 s (à remplacer par Laravel Reverb pour du vrai temps réel)
                        setInterval(() => this.poll(), 5000);
                    },

                    // ---------- Affichage ----------
                    sameAuthor(a, b) { return a && b && a.mine === b.mine && a.userId === b.userId && a.day === b.day; },
                    startsGroup(i) { return !this.sameAuthor(this.messages[i - 1], this.messages[i]); },
                    endsGroup(i) { return !this.sameAuthor(this.messages[i], this.messages[i + 1]); },
                    newDay(i) { return i === 0 || this.messages[i - 1].day !== this.messages[i].day; },
                    get lastSeenId() {
                        if (!this.direct || !this.seenUntil) return null;
                        const seen = this.messages.filter(m => m.mine && !m.pending && m.ts <= this.seenUntil);
                        return seen.length ? seen[seen.length - 1].id : null;
                    },
                    // Texte échappé, liens cliquables
                    render(text) {
                        if (!text) return '';
                        const esc = text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
                        return esc.replace(/(https?:\/\/[^\s<]+[^\s<.,;:!?)\]'"])/g, '<a href="$1" target="_blank" rel="noopener noreferrer">$1</a>');
                    },

                    // ---------- Défilement ----------
                    onScroll() {
                        const el = this.$refs.list;
                        this.atBottom = el.scrollHeight - el.scrollTop - el.clientHeight < 90;
                        if (this.atBottom) this.newCount = 0;
                    },
                    scroll(force = false, behavior = 'smooth') {
                        if (!force && !this.atBottom) return;
                        this.$nextTick(() => { const el = this.$refs.list; el.scrollTo({ top: el.scrollHeight, behavior }); this.newCount = 0; });
                    },
                    async loadOlder() {
                        if (this.loadingOlder || !this.messages.length) return;
                        this.loadingOlder = true;
                        const el = this.$refs.list, before = el.scrollHeight;
                        try {
                            const params = new URLSearchParams({ before: this.messages.find(m => typeof m.id === 'number')?.id ?? 0 });
                            if (topic) params.set('theme', topic);
                            const { data } = await window.axios.get(fetchUrl + '?' + params);
                            this.messages.unshift(...data.messages);
                            this.hasMore = data.hasMore;
                            this.$nextTick(() => { el.scrollTop = el.scrollHeight - before; });
                        } finally { this.loadingOlder = false; }
                    },

                    // ---------- Rafraîchissement ----------
                    lastId() { const real = this.messages.filter(m => typeof m.id === 'number'); return real.length ? real[real.length - 1].id : 0; },
                    async poll() {
                        const params = new URLSearchParams({ after: this.lastId() });
                        if (topic) params.set('theme', topic);
                        try {
                            const { data } = await window.axios.get(fetchUrl + '?' + params);
                            this.seenUntil = data.seenUntil ?? this.seenUntil;
                            const fresh = data.messages.filter(m => !this.messages.some(x => x.id === m.id));
                            if (!fresh.length) return;
                            this.messages.push(...fresh);
                            if (this.atBottom) this.scroll(true);
                            else this.newCount += fresh.filter(m => !m.mine).length;
                        } catch (e) { /* hors ligne : on réessaiera au prochain passage */ }
                    },

                    // ---------- Fichiers ----------
                    pickFile(file) {
                        if (!file) return;
                        if (this.filePreview) URL.revokeObjectURL(this.filePreview);
                        this.fileName = file.name;
                        this.filePreview = file.type.startsWith('image/') ? URL.createObjectURL(file) : null;
                    },
                    setFile(file) {
                        const dt = new DataTransfer(); dt.items.add(file);
                        this.$refs.file.files = dt.files;
                        this.pickFile(file);
                    },
                    dropFile(e) { this.dragging = false; const f = e.dataTransfer?.files?.[0]; if (f && this.$refs.file) this.setFile(f); },
                    pasteImage(e) { const f = [...(e.clipboardData?.files ?? [])].find(f => f.type.startsWith('image/')); if (f) { e.preventDefault(); this.setFile(f); } },
                    clearFile() { if (this.filePreview) URL.revokeObjectURL(this.filePreview); this.fileName = ''; this.filePreview = null; if (this.$refs.file) this.$refs.file.value = ''; },
                    grow() { const el = this.$refs.input; if (!el) return; el.style.height = 'auto'; el.style.height = Math.min(el.scrollHeight, 144) + 'px'; },

                    // ---------- Envoi ----------
                    send() {
                        const file = this.$refs.file?.files[0] ?? null;
                        const body = this.body.trim();
                        if (!body && !file) return;
                        const now = new Date();
                        const temp = {
                            id: 'tmp-' + now.getTime(), mine: true, pending: true, failed: false, canDelete: false,
                            body, author: '', initials: '', avatar: null, userId: null, official: false,
                            time: now.toTimeString().slice(0, 5), ts: Math.floor(now / 1000),
                            day: today(), dayLabel: 'Aujourd’hui',
                            file: file ? { name: file.name, image: file.type.startsWith('image/'), preview: this.filePreview, url: null, size: null } : null,
                        };
                        if (file) pendingFiles.set(temp.id, file);
                        this.messages.push(temp);
                        this.body = ''; this.fileName = ''; this.filePreview = null; if (this.$refs.file) this.$refs.file.value = '';
                        this.$nextTick(() => this.grow());
                        this.scroll(true);
                        this.upload(this.messages[this.messages.length - 1]);
                    },
                    // m : le message tel que stocké dans la liste (objet réactif)
                    async upload(m) {
                        this.error = '';
                        const form = new FormData();
                        if (m.body) form.append('body', m.body);
                        if (pendingFiles.has(m.id)) form.append('file', pendingFiles.get(m.id));
                        if (topic) form.append('topic', topic);
                        try {
                            const { data } = await window.axios.post(postUrl, form, { headers: { Accept: 'application/json' } });
                            pendingFiles.delete(m.id);
                            const idx = this.messages.findIndex(x => x.id === m.id);
                            if (this.messages.some(x => x.id === data.id)) this.messages.splice(idx, 1);
                            else this.messages.splice(idx, 1, data);
                        } catch (e) {
                            m.pending = false; m.failed = true;
                            this.error = e.response?.data?.message ?? 'Envoi impossible. Vérifiez votre connexion, puis réessayez.';
                            setTimeout(() => this.error = '', 6000);
                        }
                    },
                    retry(m) { m.failed = false; m.pending = true; this.upload(m); },
                    async remove(m) {
                        if (!confirm('Supprimer ce message ? Cette action est définitive.')) return;
                        try {
                            await window.axios.delete(deleteUrl.replace('__ID__', m.id));
                            this.messages = this.messages.filter(x => x.id !== m.id);
                        } catch (e) {
                            this.error = 'Suppression impossible pour le moment.';
                            setTimeout(() => this.error = '', 5000);
                        }
                    },
                };
            }
        </script>
    @endpush
</x-layouts.app>
