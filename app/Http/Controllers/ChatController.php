<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ChatController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        return view('app.chat.index', ['user' => $user, ...$this->conversationList($user, $request->query('filtre', 'tous'))]);
    }

    /** Liste des conversations (page Chat, et colonne de gauche d'une conversation sur ordinateur) */
    private function conversationList(User $user, string $filter): array
    {
        $conversations = Conversation::forUser($user)
            ->with(['latestMessage.user', 'users'])
            ->orderByRaw("type = 'super' desc")
            ->orderByDesc('last_message_at')
            ->get();

        $publicGroups = Conversation::where('type', Conversation::GROUP)->where('is_public', true)
            ->whereDoesntHave('users', fn ($q) => $q->whereKey($user->id))
            ->withCount('users')->latest()->take(5)->get();

        return [
            'filter' => $filter,
            'super' => $conversations->firstWhere('type', Conversation::SUPER),
            'groups' => $conversations->where('type', Conversation::GROUP),
            'directs' => $conversations->where('type', Conversation::DIRECT),
            'publicGroups' => $publicGroups,
        ];
    }

    public function show(Request $request, Conversation $conversation): View
    {
        $user = $request->user();
        abort_unless($conversation->hasMember($user), 403);

        $topic = $conversation->isSuper() ? $request->query('theme', 'annonces') : null;
        if ($topic && ! array_key_exists($topic, Conversation::TOPICS)) {
            $topic = 'annonces';
        }

        $messages = $conversation->messages()->with('user')
            ->when($topic, fn ($q) => $q->where('topic', $topic))
            ->latest('id')->take(50)->get()->reverse()->values();

        $pinned = $conversation->messages()->where('is_pinned', true)->latest()->first();

        $conversation->users()->updateExistingPivot($user->id, ['last_read_at' => now()]);
        $conversation->load('users');

        return view('app.chat.show', [
            'conversation' => $conversation,
            'user' => $user,
            'topic' => $topic,
            'pinned' => $pinned,
            'canPost' => $conversation->canPost($user, $topic),
            'initialMessages' => $messages->map->toChatArray($user),
            'membersCount' => $conversation->users->count(),
            'list' => $this->conversationList($user, 'tous'),
            'members' => $conversation->isSuper()
                ? collect()
                : $conversation->users->sortBy(fn ($u) => [$u->pivot->role === 'admin' ? 0 : 1, $u->first_name])->values(),
            'seenUntil' => $conversation->seenUntilFor($user),
            'hasMore' => $messages->count() === 50,
        ]);
    }

    /**
     * Rafraîchissement : nouveaux messages après l'id « after »,
     * ou 50 messages plus anciens avant l'id « before » (historique).
     */
    public function messages(Request $request, Conversation $conversation): JsonResponse
    {
        $user = $request->user();
        abort_unless($conversation->hasMember($user), 403);

        $base = $conversation->messages()->with('user')
            ->when($conversation->isSuper(), fn ($q) => $q->where('topic', $request->query('theme', 'annonces')));

        if ($before = (int) $request->query('before')) {
            $older = (clone $base)->where('id', '<', $before)->latest('id')->take(50)->get()->reverse()->values();

            return response()->json([
                'messages' => $older->map->toChatArray($user),
                'hasMore' => $older->count() === 50,
            ]);
        }

        $messages = $base->where('id', '>', (int) $request->query('after', 0))->orderBy('id')->take(100)->get();

        if ($messages->isNotEmpty()) {
            $conversation->users()->updateExistingPivot($user->id, ['last_read_at' => now()]);
        }

        return response()->json([
            'messages' => $messages->map->toChatArray($user),
            'seenUntil' => $conversation->seenUntilFor($user),
        ]);
    }

    /** Suppression d'un message par son auteur (ou par l'équipe) */
    public function destroy(Request $request, Conversation $conversation, Message $message): JsonResponse
    {
        $user = $request->user();
        abort_unless($message->conversation_id === $conversation->id, 404);
        abort_unless($message->user_id === $user->id || $user->isStaff(), 403);

        if ($message->attachment_path) {
            Storage::disk('public')->delete($message->attachment_path);
        }
        $message->delete();

        return response()->json(['deleted' => $message->id]);
    }

    /** Recherche de membres pour démarrer une conversation privée */
    public function members(Request $request): JsonResponse
    {
        $me = $request->user();
        $q = trim((string) $request->query('q'));

        $users = User::whereKeyNot($me->id)
            ->when($q !== '', fn ($w) => $w->where(fn ($x) => $x->where('first_name', 'like', "%{$q}%")->orWhere('last_name', 'like', "%{$q}%")))
            ->orderByRaw("role in ('agent', 'admin') desc")
            ->orderBy('first_name')
            ->take(12)->get();

        return response()->json($users->map(fn (User $u) => [
            'id' => $u->id,
            'name' => $u->name,
            'initials' => $u->initials,
            'avatar' => $u->avatar_url,
            'role' => $u->role_label,
            'url' => route('chat.direct', $u),
        ]));
    }

    public function store(Request $request, Conversation $conversation): JsonResponse|RedirectResponse
    {
        $user = $request->user();
        abort_unless($conversation->hasMember($user), 403);

        $data = $request->validate([
            'body' => ['nullable', 'string', 'max:4000', 'required_without:file'],
            'file' => ['nullable', 'file', 'max:'.config('backtoburkina.uploads.max_kb'), 'mimes:pdf,jpg,jpeg,png,webp,gif,doc,docx,xls,xlsx'],
            'topic' => [$conversation->isSuper() ? 'required' : 'nullable', Rule::in(array_keys(Conversation::TOPICS))],
        ]);
        $topic = $conversation->isSuper() ? $data['topic'] : null;

        abort_unless($conversation->canPost($user, $topic), 403, 'Seule l’équipe Travel Express publie dans ce thème.');

        $attrs = ['user_id' => $user->id, 'topic' => $topic, 'body' => $data['body'] ?? null];
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $attrs['attachment_path'] = $file->store('chat/'.$conversation->id, 'public');
            $attrs['attachment_name'] = $file->getClientOriginalName();
            $attrs['attachment_size'] = $file->getSize();
        }

        $message = $conversation->messages()->create($attrs)->load('user');

        if ($request->expectsJson()) {
            return response()->json($message->toChatArray($user), 201);
        }

        return back();
    }

    /** Ouvre (ou crée) une conversation privée avec un membre */
    public function direct(Request $request, User $user): RedirectResponse
    {
        $me = $request->user();
        abort_if($me->id === $user->id, 422);

        $conversation = Conversation::where('type', Conversation::DIRECT)
            ->whereHas('users', fn ($q) => $q->whereKey($me->id))
            ->whereHas('users', fn ($q) => $q->whereKey($user->id))
            ->first();

        if (! $conversation) {
            $conversation = Conversation::create(['type' => Conversation::DIRECT, 'created_by' => $me->id]);
            $conversation->users()->attach([$me->id, $user->id]);
        }

        return redirect()->route('chat.show', $conversation);
    }
}
