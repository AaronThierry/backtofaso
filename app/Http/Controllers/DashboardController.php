<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\Conversation;
use App\Models\Dossier;
use App\Models\TalentProfile;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Tableau de bord : contenu adapté au profil (candidat, diplômé, recruteur, équipe) */
class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $super = Conversation::superGroup();
        $feed = fn (string $topic, int $take = 3) => $super->messages()->with('user')->where('topic', $topic)->latest()->take($take)->get();

        $data = [
            'user' => $user,
            'super' => $super,
            'unread' => $user->unreadMessagesCount(),
            'announcements' => $feed('annonces'),
            'dossier' => null,
            'profile' => null,
            'jobs' => collect(),
            'scholarships' => collect(),
            'talents' => collect(),
            'stats' => [],
        ];

        if ($user->needsDossier()) {
            $data['dossier'] = $user->dossier?->load(['documents', 'advisor']);
            $data['scholarships'] = $feed('bourses');
        }

        if ($user->talentProfile) {
            $data['profile'] = $user->talentProfile->load('user');
            $data['jobs'] = $feed('emplois');
        }

        if ($user->isRecruiter() || $user->isStaff()) {
            $data['talents'] = TalentProfile::visible()->with('user')->latest()->take(4)->get();
            $data['stats']['talents'] = TalentProfile::visible()->count();
            $data['stats']['available'] = TalentProfile::visible()->where('availability', 'available')->count();
            $data['jobs'] = $feed('emplois');
        }

        if ($user->isStaff()) {
            $data['stats']['pending_talents'] = TalentProfile::whereNull('verified_at')->count();
            $data['stats']['contacts'] = ContactMessage::whereNull('handled_at')->count();
            $data['stats']['dossiers'] = Dossier::where('status', 'open')->count();
        }

        return view('app.dashboard', $data);
    }
}
