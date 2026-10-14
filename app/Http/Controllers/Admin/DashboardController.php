<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\Dossier;
use App\Models\DossierDocument;
use App\Models\TalentProfile;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'stats' => [
                'users' => User::count(),
                'newUsers' => User::where('created_at', '>=', now()->subDays(7))->count(),
                'openDossiers' => Dossier::where('status', 'open')->count(),
                'docsToReview' => DossierDocument::where('status', DossierDocument::PENDING)->count(),
                'pendingTalents' => TalentProfile::whereNull('verified_at')->count(),
                'contacts' => ContactMessage::whereNull('handled_at')->count(),
            ],
            'byRole' => User::selectRaw('role, count(*) as total')->groupBy('role')->pluck('total', 'role'),
            'bySteps' => Dossier::where('status', 'open')->selectRaw('current_step, count(*) as total')->groupBy('current_step')->pluck('total', 'current_step'),
            'docsQueue' => DossierDocument::with('dossier.user')->where('status', DossierDocument::PENDING)->oldest('submitted_at')->take(6)->get(),
            'recentUsers' => User::latest()->take(6)->get(),
            'recentContacts' => ContactMessage::whereNull('handled_at')->latest()->take(4)->get(),
        ]);
    }
}
