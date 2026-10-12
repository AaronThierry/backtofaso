@php
    $topicIcons = ['annonces' => 'bell', 'entraide' => 'help', 'emplois' => 'briefcase', 'bourses' => 'graduation'];
@endphp
<x-layouts.app :title="$conversation->displayNameFor($user)" active="chat" :bare="true">
    <div class="lg:grid lg:h-dvh lg:grid-cols-[minmax(320px,390px)_minmax(0,1fr)]">
        {{-- Ordinateur : liste des conversations à gauche --}}
        <div class="scrollbar-thin hidden overflow-y-auto border-r border-ink-200/70 bg-white p-5 lg:block">
            @include('app.chat._list', $list + ['activeId' => $conversation->id])
        </div>

        <div class="flex h-dvh min-w-0 flex-col bg-canvas"
             x-data="chatRoom({
                fetchUrl: @js(route('chat.messages', $conversation)),
                postUrl: @js(route('chat.store', $conversation)),
                topic: @js($topic),
                initial: @js($initialMessages),
             })">
            {{-- En-tête --}}
            <header class="z-10 flex flex-col gap-3 border-b border-ink-200/70 bg-white/90 px-3 pb-3 pt-[calc(env(safe-area-inset-top)+10px)] backdrop-blur-lg lg:px-6 lg:pt-4">
                <div class="flex items-center gap-3">
                    <a href="{{ route('chat.index') }}" aria-label="Retour" class="flex size-10 items-center justify-center rounded-full text-ink-700 hover:bg-ink-50 lg:hidden"><x-icon name="back" :size="22" /></a>
                    @if ($conversation->isSuper())
                        <x-logo :size="44" />
                    @else
                        <x-avatar :initials="$conversation->initials($user)" :size="44" :tone="$conversation->type === 'direct' ? 'brand' : 'ink'" :square="$conversation->type !== 'direct'" />
                    @endif
                    <span class="flex min-w-0 flex-1 flex-col gap-0.5">
                        <span class="truncate font-display text-[17px] font-bold text-navy-950">{{ $conversation->displayNameFor($user) }}</span>
                        <span class="flex items-center gap-1.5 text-xs text-ink-500">
                            <span class="size-1.5 rounded-full bg-ok-500"></span>
                            @if ($conversation->isSuper()) Super groupe · @elseif ($conversation->type === 'group') Groupe {{ $conversation->is_public ? 'public' : 'privé' }} · @else Conversation privée · @endif
                            {{ $membersCount }} membres
                        </span>
                    </span>
                </div>
                @if ($conversation->isSuper())
                    <nav class="-mx-1 flex gap-2 overflow-x-auto px-1 pb-0.5 scrollbar-thin" aria-label="Thèmes">
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
            <div x-ref="list" class="bg-dots scrollbar-thin flex flex-1 flex-col overflow-y-auto px-3 py-5 lg:px-8 lg:py-7">
                <template x-if="messages.length === 0">
                    <div class="m-auto flex flex-col items-center gap-3 text-center">
                        <span class="flex size-16 items-center justify-center rounded-2xl bg-white text-brand-600 shadow-card"><x-icon name="chat" :size="28" /></span>
                        <span class="font-display text-lg font-bold text-navy-950">Aucun message pour l’instant</span>
                        <span class="max-w-xs text-sm text-ink-600">{{ $canPost ? 'Lancez la conversation, votre message sera le premier.' : 'Les publications de l’équipe apparaîtront ici.' }}</span>
                    </div>
                </template>
                <template x-for="(m, i) in messages" :key="m.id">
                    <div class="flex items-end gap-2" :class="[m.mine && 'justify-end', startsGroup(i) ? 'mt-4 first:mt-0' : 'mt-1']">
                        <template x-if="!m.mine">
                            <span class="flex size-8 shrink-0 items-center justify-center rounded-full text-[11px] font-bold"
                                  :class="endsGroup(i) ? 'bg-linear-to-br from-brand-100 to-brand-200 text-brand-800' : 'invisible'" x-text="m.initials"></span>
                        </template>
                        <div class="flex max-w-[80%] flex-col gap-1.5 px-3.5 py-2.5 lg:max-w-[62%] xl:max-w-[52%]"
                             :class="[m.mine ? 'rounded-[20px] bg-linear-to-br from-brand-500 to-brand-600 text-white shadow-[0_6px_16px_-8px_rgb(0_113_227/0.6)]' : 'rounded-[20px] bg-white text-ink-900 shadow-card ring-1 ring-ink-900/[0.04]',
                                      endsGroup(i) && (m.mine ? 'rounded-br-md' : 'rounded-bl-md')]">
                            <template x-if="!m.mine && startsGroup(i)">
                                <span class="flex items-center gap-1.5">
                                    <span class="text-[12.5px] font-bold text-brand-700" x-text="m.author"></span>
                                    <span x-show="m.official" class="badge-gold px-1.5 py-0.5 text-[10px]">Officiel</span>
                                </span>
                            </template>
                            <span x-show="m.body" class="whitespace-pre-line break-words text-[14.5px] leading-snug" x-text="m.body"></span>
                            <template x-if="m.file">
                                <a :href="m.file.url" target="_blank" rel="noopener" class="flex items-center gap-3 rounded-xl p-2.5 transition" :class="m.mine ? 'bg-white/15 hover:bg-white/25' : 'bg-ink-50 hover:bg-ink-100'">
                                    <span class="flex size-10 shrink-0 items-center justify-center rounded-lg" :class="m.mine ? 'bg-white text-brand-600' : 'bg-err-50 text-err-700'"><x-icon name="file" :size="19" /></span>
                                    <span class="flex min-w-0 flex-col"><span class="truncate text-[13px] font-bold" x-text="m.file.name"></span><span class="text-[11.5px] opacity-75" x-text="m.file.size"></span></span>
                                </a>
                            </template>
                            <span class="self-end text-[10.5px] font-medium tabular-nums" :class="m.mine ? 'text-white/75' : 'text-ink-500'" x-text="m.time"></span>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Saisie --}}
            @if ($canPost)
                <form @submit.prevent="send" class="flex flex-col gap-2 border-t border-ink-200/70 bg-white px-3 pb-[calc(env(safe-area-inset-bottom)+12px)] pt-3 lg:px-6 lg:pb-5">
                    <div x-show="error" x-cloak x-transition class="flex items-center gap-2 rounded-xl bg-err-50 px-3 py-2 text-[13px] font-semibold text-err-700">
                        <x-icon name="help" :size="16" /> <span x-text="error"></span>
                    </div>
                    <div x-show="fileName" x-cloak class="flex items-center gap-2.5 self-start rounded-xl bg-brand-50 py-1.5 pl-2 pr-1.5 text-[13px] font-semibold text-brand-800 ring-1 ring-brand-100">
                        <x-icon name="file" :size="16" /> <span class="max-w-[220px] truncate" x-text="fileName"></span>
                        <button type="button" @click="clearFile()" aria-label="Retirer le fichier" class="flex size-6 items-center justify-center rounded-md hover:bg-brand-100"><x-icon name="close" :size="14" /></button>
                    </div>
                    <div class="flex items-end gap-2">
                        <label class="flex size-11 shrink-0 cursor-pointer items-center justify-center rounded-full text-ink-600 transition hover:bg-ink-100 hover:text-ink-900" aria-label="Joindre un fichier" title="Joindre un fichier">
                            <x-icon name="plus" :size="22" stroke-width="2" />
                            <input type="file" x-ref="file" class="sr-only" @change="fileName = $event.target.files[0]?.name ?? ''">
                        </label>
                        <label class="flex min-h-11 flex-1 items-center rounded-[22px] bg-ink-50 px-4 py-2 ring-1 ring-ink-200/70 transition focus-within:bg-white focus-within:ring-2 focus-within:ring-brand-300">
                            <span class="sr-only">Votre message</span>
                            <textarea x-ref="input" x-model="body" rows="1"
                                      @input="grow()" @keydown.enter="if (!$event.shiftKey) { $event.preventDefault(); send(); }"
                                      placeholder="{{ 'Écrire'.($topic ? ' dans '.\App\Models\Conversation::TOPICS[$topic][0] : '').'…' }}"
                                      class="max-h-36 w-full resize-none bg-transparent text-[15px] leading-snug outline-none placeholder:text-ink-400"></textarea>
                        </label>
                        <button type="submit" aria-label="Envoyer" :disabled="sending || (!body.trim() && !fileName)"
                                class="flex size-11 shrink-0 items-center justify-center rounded-full bg-linear-to-br from-brand-500 to-brand-600 text-white shadow-glow transition hover:scale-105 disabled:scale-100 disabled:opacity-40 disabled:shadow-none">
                            <x-icon name="send" :size="19" stroke-width="2.2" x-show="!sending" />
                            <span x-show="sending" x-cloak class="size-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                        </button>
                    </div>
                    <span class="hidden px-14 text-[11px] text-ink-400 lg:block">Entrée pour envoyer · Maj + Entrée pour aller à la ligne</span>
                </form>
            @else
                <div class="flex items-center justify-center gap-2 border-t border-ink-200/70 bg-white px-4 pb-[calc(env(safe-area-inset-bottom)+18px)] pt-4 text-[13.5px] font-semibold text-ink-500">
                    <x-icon name="lock" :size="16" /> Seule l’équipe Travel Express publie dans ce thème
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            function chatRoom({ fetchUrl, postUrl, topic, initial }) {
                return {
                    messages: initial, body: '', fileName: '', sending: false, error: '', timer: null,
                    init() {
                        this.scroll();
                        // Rafraîchissement toutes les 5 s (à remplacer par Laravel Reverb pour du vrai temps réel)
                        this.timer = setInterval(() => this.poll(), 5000);
                    },
                    // Messages consécutifs d'une même personne : regroupés visuellement
                    sameAuthor(a, b) { return a && b && a.mine === b.mine && a.author === b.author; },
                    startsGroup(i) { return !this.sameAuthor(this.messages[i - 1], this.messages[i]); },
                    endsGroup(i) { return !this.sameAuthor(this.messages[i], this.messages[i + 1]); },
                    lastId() { return this.messages.length ? this.messages[this.messages.length - 1].id : 0; },
                    scroll() { this.$nextTick(() => { this.$refs.list.scrollTo({ top: this.$refs.list.scrollHeight, behavior: 'smooth' }); }); },
                    grow() { const el = this.$refs.input; if (!el) return; el.style.height = 'auto'; el.style.height = Math.min(el.scrollHeight, 144) + 'px'; },
                    clearFile() { this.fileName = ''; this.$refs.file.value = ''; },
                    async poll() {
                        const params = new URLSearchParams({ after: this.lastId() });
                        if (topic) params.set('theme', topic);
                        try {
                            const { data } = await window.axios.get(fetchUrl + '?' + params);
                            if (data.length) { this.messages.push(...data.filter(m => !this.messages.some(x => x.id === m.id))); this.scroll(); }
                        } catch (e) { /* hors ligne : on réessaiera au prochain passage */ }
                    },
                    async send() {
                        const file = this.$refs.file.files[0];
                        if (this.sending || (!this.body.trim() && !file)) return;
                        this.sending = true; this.error = '';
                        const form = new FormData();
                        if (this.body.trim()) form.append('body', this.body);
                        if (file) form.append('file', file);
                        if (topic) form.append('topic', topic);
                        try {
                            const { data } = await window.axios.post(postUrl, form, { headers: { Accept: 'application/json' } });
                            if (!this.messages.some(x => x.id === data.id)) this.messages.push(data);
                            this.body = ''; this.clearFile();
                            this.$nextTick(() => this.grow());
                            this.scroll();
                        } catch (e) {
                            this.error = e.response?.data?.message ?? 'Envoi impossible. Vérifiez votre connexion.';
                            setTimeout(() => this.error = '', 5000);
                        } finally {
                            this.sending = false;
                        }
                    },
                };
            }
        </script>
    @endpush
</x-layouts.app>
