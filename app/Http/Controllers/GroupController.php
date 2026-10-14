<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GroupController extends Controller
{
    public function create(Request $request): View
    {
        $q = trim((string) $request->query('q'));
        $people = User::whereKeyNot($request->user()->id)
            ->when($q !== '', fn ($w) => $w->where(fn ($x) => $x->where('first_name', 'like', "%{$q}%")->orWhere('last_name', 'like', "%{$q}%")))
            ->orderBy('first_name')->take(40)->get();

        return view('app.chat.group-create', compact('people', 'q'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'description' => ['nullable', 'string', 'max:200'],
            'is_public' => ['required', 'boolean'],
            'members' => ['nullable', 'array'],
            'members.*' => ['integer', 'exists:users,id'],
            'avatar' => ['nullable', 'image', 'max:4096'],
        ]);

        $me = $request->user();
        $group = Conversation::create([
            'type' => Conversation::GROUP,
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'is_public' => (bool) $data['is_public'],
            'created_by' => $me->id,
            'avatar_path' => $request->hasFile('avatar') ? $request->file('avatar')->store('groups', 'public') : null,
        ]);

        $members = collect($data['members'] ?? [])->reject(fn ($id) => (int) $id === $me->id)
            ->mapWithKeys(fn ($id) => [(int) $id => ['role' => 'member']])->all();
        $group->users()->attach([$me->id => ['role' => 'admin']] + $members);

        $group->messages()->create(['user_id' => $me->id, 'body' => $me->first_name.' a créé le groupe « '.$group->name.' ».']);

        return redirect()->route('chat.show', $group);
    }

    /** Inviter des membres dans un groupe (administrateurs du groupe et équipe Travel Express) */
    public function addMembers(Request $request, Conversation $conversation): RedirectResponse
    {
        $me = $request->user();
        abort_unless($conversation->type === Conversation::GROUP && $conversation->isAdmin($me), 403);

        $data = $request->validate([
            'members' => ['required', 'array', 'max:50'],
            'members.*' => ['integer', 'exists:users,id'],
        ], ['members.required' => 'Choisissez au moins une personne à ajouter.']);

        $current = $conversation->users()->pluck('users.id');
        $new = User::whereIn('id', $data['members'])->whereNotIn('id', $current)->get();

        if ($new->isEmpty()) {
            return redirect()->route('chat.show', $conversation)->with('status', 'Ces personnes font déjà partie du groupe.');
        }

        $conversation->users()->attach($new->mapWithKeys(fn ($u) => [$u->id => ['role' => 'member']])->all());

        $names = $new->pluck('first_name');
        $list = $names->count() > 1 ? $names->slice(0, -1)->join(', ').' et '.$names->last() : $names->first();
        $conversation->messages()->create(['user_id' => $me->id, 'body' => $me->first_name.' a ajouté '.$list.' au groupe.']);

        return redirect()->route('chat.show', $conversation)
            ->with('status', $new->count() > 1 ? $new->count().' personnes ont été ajoutées au groupe.' : $list.' a été ajouté(e) au groupe.');
    }

    /** Retirer un membre d'un groupe (administrateurs du groupe et équipe Travel Express) */
    public function removeMember(Request $request, Conversation $conversation, User $user): RedirectResponse
    {
        $me = $request->user();
        abort_unless($conversation->type === Conversation::GROUP && $conversation->isAdmin($me), 403);
        abort_if($user->id === $me->id, 422, 'Pour partir du groupe, utilisez « Quitter le groupe ».');

        $conversation->users()->detach($user->id);
        $conversation->messages()->create(['user_id' => $me->id, 'body' => $me->first_name.' a retiré '.$user->first_name.' du groupe.']);

        return redirect()->route('chat.show', $conversation)->with('status', $user->first_name.' a été retiré(e) du groupe.');
    }

    public function leave(Request $request, Conversation $conversation): RedirectResponse
    {
        $me = $request->user();
        abort_unless($conversation->type === Conversation::GROUP && $conversation->hasMember($me), 403);

        $conversation->users()->detach($me->id);
        $conversation->messages()->create(['user_id' => $me->id, 'body' => $me->first_name.' a quitté le groupe.']);

        return redirect()->route('chat.index')->with('status', 'Vous avez quitté le groupe « '.$conversation->name.' ».');
    }

    public function join(Request $request, Conversation $conversation): RedirectResponse
    {
        abort_unless($conversation->type === Conversation::GROUP && $conversation->is_public, 403);
        $conversation->users()->syncWithoutDetaching([$request->user()->id => ['role' => 'member']]);

        return redirect()->route('chat.show', $conversation);
    }
}
