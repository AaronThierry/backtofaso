<?php

namespace App\Http\Controllers;

use App\Mail\GroupInvitationMail;
use App\Models\Conversation;
use App\Models\GroupInvitation;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

/** Invitations à un groupe par e-mail, avec un lien personnel lié au compte invité */
class GroupInvitationController extends Controller
{
    /** Envoi des invitations (administrateurs du groupe et équipe) */
    public function store(Request $request, Conversation $conversation): RedirectResponse
    {
        $me = $request->user();
        abort_unless($conversation->type === Conversation::GROUP && $conversation->isAdmin($me), 403);

        // « a@x.bf, b@y.bf » ou une adresse par ligne
        $emails = collect(preg_split('/[\s,;]+/', (string) $request->input('emails')))
            ->map(fn ($e) => mb_strtolower(trim($e)))->filter()->unique()->values();

        $validator = Validator::make(['emails' => $emails->all()], [
            'emails' => ['required', 'array', 'max:20'],
            'emails.*' => ['email'],
        ], [
            'emails.required' => 'Indiquez au moins une adresse e-mail.',
            'emails.max' => '20 adresses au maximum par envoi.',
            'emails.*.email' => 'L’adresse « :input » n’est pas valide.',
        ]);
        if ($validator->fails()) {
            return redirect()->route('chat.show', $conversation)->withErrors($validator, 'invite')->withInput()->with('openInfo', true);
        }

        $sent = 0;
        $already = [];
        $failed = [];
        foreach ($emails as $email) {
            $user = User::where('email', $email)->first();
            if ($user && $conversation->hasMember($user)) {
                $already[] = $email;
                continue;
            }

            [$invitation, $token] = GroupInvitation::issue($conversation, $me, $email);
            try {
                Mail::to($email)->send(new GroupInvitationMail($invitation->load('conversation', 'inviter'), route('invitations.show', $token), (bool) $user));
                $sent++;
            } catch (\Throwable $e) {
                report($e);
                $failed[] = $email;
            }
        }

        $message = collect([
            $sent ? ($sent > 1 ? $sent.' invitations envoyées.' : 'Invitation envoyée.') : null,
            $already ? implode(', ', $already).' fait déjà partie du groupe.' : null,
            $failed ? 'Échec de l’envoi à '.implode(', ', $failed).'.' : null,
        ])->filter()->join(' ');

        return redirect()->route('chat.show', $conversation)->with('status', $message)->with('openInfo', true);
    }

    public function destroy(Request $request, Conversation $conversation, GroupInvitation $invitation): RedirectResponse
    {
        abort_unless($invitation->conversation_id === $conversation->id && $conversation->isAdmin($request->user()), 403);
        $invitation->delete();

        return redirect()->route('chat.show', $conversation)->with('status', 'Invitation annulée.')->with('openInfo', true);
    }

    /** Page du lien reçu par e-mail */
    public function show(Request $request, string $token): View|RedirectResponse
    {
        $invitation = GroupInvitation::findByToken($token)?->load('conversation', 'inviter');
        $user = $request->user();

        if (! $invitation) {
            return view('auth.invitation', ['state' => 'invalid', 'invitation' => null, 'token' => $token]);
        }
        if ($invitation->accepted_at) {
            if ($user && $invitation->conversation->hasMember($user)) {
                return redirect()->route('chat.show', $invitation->conversation);
            }

            return view('auth.invitation', ['state' => 'used', 'invitation' => $invitation, 'token' => $token]);
        }
        if (! $invitation->isUsable()) {
            return view('auth.invitation', ['state' => 'expired', 'invitation' => $invitation, 'token' => $token]);
        }

        if (! $user) {
            // Après la connexion, retour sur cette page
            session()->put('url.intended', $request->fullUrl());

            return view('auth.invitation', [
                'state' => 'guest',
                'invitation' => $invitation,
                'token' => $token,
                'hasAccount' => User::where('email', $invitation->email)->exists(),
            ]);
        }

        return view('auth.invitation', [
            'state' => $invitation->isFor($user) ? 'ready' : 'wrong-account',
            'invitation' => $invitation,
            'token' => $token,
        ]);
    }

    /** Acceptation : seulement par le compte invité */
    public function accept(Request $request, string $token): RedirectResponse
    {
        $invitation = GroupInvitation::findByToken($token);
        $user = $request->user();

        abort_unless($invitation && $invitation->isUsable(), 410, 'Cette invitation n’est plus valable.');
        abort_unless($invitation->isFor($user), 403, 'Cette invitation est destinée à un autre compte.');

        $invitation->accept($user);

        return redirect()->route('chat.show', $invitation->conversation)
            ->with('status', 'Bienvenue dans le groupe « '.$invitation->conversation->name.' » !');
    }
}
