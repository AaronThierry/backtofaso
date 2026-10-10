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

    public function join(Request $request, Conversation $conversation): RedirectResponse
    {
        abort_unless($conversation->type === Conversation::GROUP && $conversation->is_public, 403);
        $conversation->users()->syncWithoutDetaching([$request->user()->id => ['role' => 'member']]);

        return redirect()->route('chat.show', $conversation);
    }
}
