<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TalentProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Vérification des profils du vivier de talents */
class TalentController extends Controller
{
    public function index(Request $request): View
    {
        $filter = in_array($request->query('filtre'), ['en-attente', 'verifies', 'tous'], true) ? $request->query('filtre') : 'en-attente';
        $q = trim((string) $request->query('q'));

        $profiles = TalentProfile::with('user')
            ->when($filter === 'en-attente', fn ($w) => $w->whereNull('verified_at'))
            ->when($filter === 'verifies', fn ($w) => $w->whereNotNull('verified_at'))
            ->when($q !== '', fn ($w) => $w->where(fn ($x) => $x->where('headline', 'like', "%{$q}%")
                ->orWhereHas('user', fn ($u) => $u->where('first_name', 'like', "%{$q}%")->orWhere('last_name', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%"))))
            ->latest('updated_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.talents.index', [
            'profiles' => $profiles,
            'filter' => $filter,
            'q' => $q,
            'counts' => [
                'en-attente' => TalentProfile::whereNull('verified_at')->count(),
                'verifies' => TalentProfile::whereNotNull('verified_at')->count(),
                'tous' => TalentProfile::count(),
            ],
        ]);
    }

    public function verify(Request $request, TalentProfile $profile): RedirectResponse
    {
        $verify = $request->boolean('verify');
        $profile->update(['verified_at' => $verify ? now() : null]);

        return back()->with('status', $verify
            ? 'Profil de '.$profile->user->name.' vérifié'.($profile->is_public ? ' : il est visible des recruteurs.' : ' (masqué tant que le diplômé ne le rend pas visible).')
            : 'Vérification retirée pour '.$profile->user->name.'.');
    }
}
