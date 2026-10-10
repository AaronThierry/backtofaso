<x-layouts.app :title="$conversation->displayNameFor($user)" active="chat" :bare="true">
    <div class="flex h-dvh flex-col bg-ink-50"
         x-data="chatRoom({
            fetchUrl: @js(route('chat.messages', $conversation)),
            postUrl: @js(route('chat.store', $conversation)),
            topic: @js($topic),
            initial: @js($initialMessages),
         })">
        {{-- En-tête --}}
        <header class="flex flex-col gap-3 border-b border-ink-200 bg-white px-4 pb-3 pt-[calc(env(safe-area-inset-top)+12px)]">
            <div class="flex items-center gap-2.5">
                <a href="{{ route('chat.index') }}" aria-label="Retour" class="flex size-11 items-center justify-center rounded-full"><x-icon name="back" :size="22" /></a>
                @if ($conversation->isSuper())
                    <x-logo :size="42" />
                @else
                    <x-avatar :initials="$conversation->initials($user)" :size="42" tone="ink" />
                @endif
                <span class="flex min-w-0 flex-1 flex-col">
                    <span class="truncate text-[15.5px] font-bold">{{ $conversation->displayNameFor($user) }}</span>
                    <span class="text-xs text-ink-600">
                        @if ($conversation->isSuper()) Super groupe · @elseif ($conversation->type === 'group') Groupe {{ $conversation->is_public ? 'public' : 'privé' }} · @endif
                        {{ $membersCount }} membres
                    </span>
                </span>
            </div>
            @if ($conversation->isSuper())
                <nav class="flex gap-2 overflow-x-auto" aria-label="Thèmes">
                    @foreach (\App\Models\Conversation::TOPICS as $key => [$label])
                        <a href="{{ route('chat.show', [$conversation, 'theme' => $key]) }}"
                           @class(['inline-flex min-h-9 shrink-0 items-center rounded-full border px-3.5 text-[13px] font-semibold', 'border-brand-600 bg-brand-600 text-white' => $topic === $key, 'border-ink-200 bg-white' => $topic !== $key])>{{ $label }}</a>
                    @endforeach
                </nav>
            @endif
        </header>

        @if ($pinned)
            <div class="flex items-center gap-2.5 bg-gold-50 px-4 py-2.5 text-[13px] font-semibold text-gold-700">
                <x-icon name="pin" :size="16" /> <span class="truncate">Épinglé : {{ $pinned->body }}</span>
            </div>
        @endif

        {{-- Messages --}}
        <div x-ref="list" class="flex flex-1 flex-col gap-3 overflow-y-auto px-4 py-4">
            <template x-if="messages.length === 0">
                <p class="m-auto text-sm text-ink-600">Aucun message pour l’instant.</p>
            </template>
            <template x-for="m in messages" :key="m.id">
                <div class="flex items-end gap-2" :class="m.mine && 'justify-end'">
                    <template x-if="!m.mine">
                        <span class="flex size-[34px] shrink-0 items-center justify-center rounded-full bg-brand-100 text-xs font-bold text-brand-700" x-text="m.initials"></span>
                    </template>
                    <div class="flex max-w-[78%] flex-col gap-1.5 rounded-2xl px-3 py-2.5 lg:max-w-[60%]"
                         :class="m.mine ? 'rounded-br-sm bg-brand-600 text-white' : 'rounded-bl-sm bg-white'">
                        <template x-if="!m.mine">
                            <span class="flex items-center gap-1.5">
                                <span class="text-[12.5px] font-bold text-brand-700" x-text="m.author"></span>
                                <span x-show="m.official" class="badge-gold px-1.5 py-0.5 text-[10.5px]">Officiel</span>
                            </span>
                        </template>
                        <span x-show="m.body" class="whitespace-pre-line text-sm leading-snug" x-text="m.body"></span>
                        <template x-if="m.file">
                            <a :href="m.file.url" target="_blank" class="flex items-center gap-2.5 rounded-xl p-2.5" :class="m.mine ? 'bg-white/15' : 'bg-ink-50'">
                                <span class="flex size-9 items-center justify-center rounded-[10px] bg-err-50 text-[10px] font-bold text-err-700">FICHIER</span>
                                <span class="flex flex-col"><span class="text-[13px] font-bold" x-text="m.file.name"></span><span class="text-[11.5px] opacity-80" x-text="m.file.size"></span></span>
                            </a>
                        </template>
                        <span class="self-end text-[11px]" :class="m.mine ? 'text-white/80' : 'text-ink-600'" x-text="m.time"></span>
                    </div>
                </div>
            </template>
        </div>

        {{-- Saisie --}}
        @if ($canPost)
            <form @submit.prevent="send" class="flex items-center gap-2 border-t border-ink-200 bg-white px-3 pb-[calc(env(safe-area-inset-bottom)+12px)] pt-2.5">
                <label class="flex size-11 shrink-0 cursor-pointer items-center justify-center rounded-full bg-ink-100" aria-label="Joindre un fichier">
                    <x-icon name="plus" stroke-width="2" />
                    <input type="file" x-ref="file" class="sr-only" @change="fileName = $event.target.files[0]?.name ?? ''">
                </label>
                <label class="flex min-h-11 flex-1 items-center rounded-full bg-ink-100 px-4">
                    <span class="sr-only">Votre message</span>
                    <input x-model="body" :placeholder="fileName ? 'Fichier : ' + fileName : @js('Écrire'.($topic ? ' dans '.\App\Models\Conversation::TOPICS[$topic][0] : '').'…')" class="w-full bg-transparent text-[15px] outline-none">
                </label>
                <button type="submit" aria-label="Envoyer" :disabled="sending" class="flex size-11 shrink-0 items-center justify-center rounded-full bg-brand-600 text-white disabled:opacity-50"><x-icon name="send" stroke-width="2" /></button>
            </form>
        @else
            <div class="flex items-center justify-center gap-2 border-t border-ink-200 bg-white px-4 pb-[calc(env(safe-area-inset-bottom)+20px)] pt-4 text-[13.5px] font-semibold text-ink-600">
                <x-icon name="lock" :size="16" /> Seule l’équipe Travel Express publie dans ce thème
            </div>
        @endif
    </div>

    @push('scripts')
        <script>
            function chatRoom({ fetchUrl, postUrl, topic, initial }) {
                return {
                    messages: initial, body: '', fileName: '', sending: false, timer: null,
                    init() {
                        this.scroll();
                        // Rafraîchissement toutes les 5 s (à remplacer par Laravel Reverb pour du vrai temps réel)
                        this.timer = setInterval(() => this.poll(), 5000);
                    },
                    lastId() { return this.messages.length ? this.messages[this.messages.length - 1].id : 0; },
                    scroll() { this.$nextTick(() => { this.$refs.list.scrollTop = this.$refs.list.scrollHeight; }); },
                    async poll() {
                        const params = new URLSearchParams({ after: this.lastId() });
                        if (topic) params.set('theme', topic);
                        const { data } = await window.axios.get(fetchUrl + '?' + params);
                        if (data.length) { this.messages.push(...data.filter(m => !this.messages.some(x => x.id === m.id))); this.scroll(); }
                    },
                    async send() {
                        const file = this.$refs.file.files[0];
                        if (!this.body.trim() && !file) return;
                        this.sending = true;
                        const form = new FormData();
                        if (this.body.trim()) form.append('body', this.body);
                        if (file) form.append('file', file);
                        if (topic) form.append('topic', topic);
                        try {
                            const { data } = await window.axios.post(postUrl, form, { headers: { Accept: 'application/json' } });
                            this.messages.push(data);
                            this.body = ''; this.fileName = ''; this.$refs.file.value = '';
                            this.scroll();
                        } catch (e) {
                            alert(e.response?.data?.message ?? 'Envoi impossible. Vérifiez votre connexion.');
                        } finally {
                            this.sending = false;
                        }
                    },
                };
            }
        </script>
    @endpush
</x-layouts.app>
