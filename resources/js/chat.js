/**
 * Liste des conversations : filtre instantané (q) et fenêtre « Nouveau message »
 * qui recherche un membre pour démarrer une conversation privée.
 */
export function registerChat(Alpine) {
    // Inviter des membres dans un groupe : recherche, en excluant ceux déjà présents
    Alpine.data('groupInvite', (membersUrl, currentIds) => ({
        open: false,
        term: '',
        results: [],
        picked: [],
        loading: false,

        get available() {
            return this.results.filter(p => !currentIds.includes(p.id));
        },

        start() {
            this.open = true;
            this.search();
            this.$nextTick(() => this.$refs.search?.focus());
        },

        async search() {
            this.loading = true;
            try {
                const { data } = await window.axios.get(membersUrl, { params: { q: this.term } });
                this.results = data;
            } catch (e) {
                this.results = [];
            } finally {
                this.loading = false;
            }
        },
    }));

    Alpine.data('newConversation', (membersUrl) => ({
        q: '',
        show: false,
        term: '',
        results: [],
        loading: false,

        open() {
            this.show = true;
            this.term = '';
            this.search();
            this.$nextTick(() => this.$refs.search?.focus());
        },

        async search() {
            this.loading = true;
            try {
                const { data } = await window.axios.get(membersUrl, { params: { q: this.term } });
                this.results = data;
            } catch (e) {
                this.results = [];
            } finally {
                this.loading = false;
            }
        },
    }));
}
