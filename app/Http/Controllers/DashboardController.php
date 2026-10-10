<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\TalentProfile;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $dossier = $user->needsDossier() ? $user->dossier()->withCount([
            'documents as missing_count' => fn ($q) => $q->whereIn('status', ['missing', 'to_complete']),
        ])->first() : null;

        $super = Conversation::superGroup();
        $announcement = $super->messages()->with('user')->where('topic', 'annonces')->latest()->first();

        $talents = $user->isRecruiter() || $user->isStaff()
            ? TalentProfile::visible()->with('user')->latest()->take(3)->get()
            : collect();

        return view('app.dashboard', compact('user', 'dossier', 'super', 'announcement', 'talents'));
    }
}
