<x-layouts.admin title="Utilisateurs" active="users">
    <div class="flex flex-col gap-6">
        <x-page-header eyebrow="Administration" title="Utilisateurs" subtitle="Recherchez un membre, changez son rôle (par exemple pour en faire un conseiller) ou supprimez son compte." />
        <x-flash />
        @if ($errors->any())
            <div class="flex items-center gap-3 rounded-2xl bg-err-50 px-4 py-3.5 text-sm font-semibold text-err-700 ring-1 ring-err-700/15"><x-icon name="help" :size="18" /> {{ $errors->first() }}</div>
        @endif

        <form method="GET" class="card flex flex-col gap-3 p-4 sm:flex-row sm:items-end">
            <label class="flex flex-1 flex-col gap-1.5"><span class="field-label">Recherche</span>
                <span class="field-group min-h-11"><x-icon name="search" :size="17" class="ml-3 text-ink-400" /><input name="q" value="{{ $q }}" placeholder="Nom, e-mail ou téléphone" class="min-w-0 flex-1 bg-transparent px-2.5 text-sm outline-none"></span></label>
            <label class="flex flex-col gap-1.5 sm:w-60"><span class="field-label">Rôle</span>
                <select name="role" class="field min-h-11 text-sm"><option value="">Tous les rôles</option>@foreach ($roles as $key => $label)<option value="{{ $key }}" @selected($role === $key)>{{ $label }} ({{ $counts[$key] ?? 0 }})</option>@endforeach</select></label>
            <button class="btn-primary min-h-11 px-5 text-sm">Filtrer</button>
        </form>

        <div class="card overflow-hidden">
            @forelse ($users as $u)
                <div class="flex flex-col gap-3 border-b border-ink-100 px-5 py-4 last:border-0 lg:flex-row lg:items-center">
                    <span class="flex min-w-0 flex-1 items-center gap-3">
                        <x-avatar :user="$u" :size="42" />
                        <span class="flex min-w-0 flex-col">
                            <span class="truncate text-[14.5px] font-bold text-navy-950">{{ $u->name }}{{ $u->id === auth()->id() ? ' (vous)' : '' }}</span>
                            <span class="truncate text-[12.5px] text-ink-500">{{ $u->email }} · {{ \App\Support\Phone::format($u->phone) }}</span>
                            <span class="text-[11.5px] text-ink-400">Inscrit {{ $u->created_at->diffForHumans() }}{{ $u->email_verified_at ? '' : ' · e-mail non vérifié' }}</span>
                        </span>
                    </span>
                    <div class="flex flex-wrap items-center gap-2">
                        <form method="POST" action="{{ route('admin.users.role', $u) }}" x-data class="flex items-center gap-2">@csrf @method('PUT')
                            <span class="sr-only">Rôle de {{ $u->name }}</span>
                            <select name="role" @change="if (confirm(@js('Changer le rôle de '.$u->first_name.' ?'))) $el.form.submit(); else $el.value = @js($u->role)" @disabled($u->id === auth()->id()) class="field min-h-10 w-56 text-sm disabled:opacity-60">
                                @foreach ($roles as $key => $label)<option value="{{ $key }}" @selected($u->role === $key)>{{ $label }}</option>@endforeach
                            </select>
                        </form>
                        @if ($u->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.users.destroy', $u) }}" onsubmit="return confirm(@js('Supprimer définitivement le compte de '.$u->name.' ? Son dossier, ses documents et ses messages seront effacés.'))">@csrf @method('DELETE')
                                <button class="flex size-10 items-center justify-center rounded-xl text-ink-400 transition hover:bg-err-50 hover:text-err-700" aria-label="Supprimer le compte de {{ $u->name }}" title="Supprimer le compte"><x-icon name="trash" :size="17" /></button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <x-empty icon="users" title="Aucun utilisateur" text="Aucun compte ne correspond à cette recherche." class="m-5" />
            @endforelse
        </div>

        {{ $users->links() }}
    </div>
</x-layouts.admin>
