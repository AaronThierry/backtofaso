<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/** Gestion des utilisateurs et de leurs rôles (administrateurs) */
class UserController extends Controller
{
    public const ROLES = [
        User::ROLE_CANDIDATE => 'Futur étudiant',
        User::ROLE_STUDENT_ABROAD => 'Étudiant à l’étranger',
        User::ROLE_GRADUATE => 'Diplômé de retour',
        User::ROLE_RECRUITER => 'Recruteur',
        User::ROLE_AGENT => 'Conseiller (équipe)',
        User::ROLE_ADMIN => 'Administrateur',
    ];

    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q'));
        $role = array_key_exists((string) $request->query('role'), self::ROLES) ? $request->query('role') : null;

        $users = User::query()
            ->when($q !== '', fn ($w) => $w->where(fn ($x) => $x->where('first_name', 'like', "%{$q}%")->orWhere('last_name', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%")->orWhere('phone', 'like', "%{$q}%")))
            ->when($role, fn ($w) => $w->where('role', $role))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'q' => $q,
            'role' => $role,
            'roles' => self::ROLES,
            'counts' => User::selectRaw('role, count(*) as total')->groupBy('role')->pluck('total', 'role'),
        ]);
    }

    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate(['role' => ['required', Rule::in(array_keys(self::ROLES))]]);
        abort_if($user->id === $request->user()->id && $data['role'] !== User::ROLE_ADMIN, 422, 'Vous ne pouvez pas retirer votre propre rôle d’administrateur.');

        $user->forceFill(['role' => $data['role']])->save();

        return back()->with('status', $user->name.' est maintenant « '.self::ROLES[$data['role']].' ».');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        abort_if($user->id === $request->user()->id, 422, 'Vous ne pouvez pas supprimer votre propre compte ici.');

        $public = array_filter([$user->avatar_path, $user->talentProfile?->cv_path, ...$user->messages()->whereNotNull('attachment_path')->pluck('attachment_path')->all()]);
        $private = $user->dossier?->documents()->whereNotNull('path')->pluck('path')->all() ?? [];
        $name = $user->name;

        $user->delete();
        Storage::disk('public')->delete($public);
        Storage::disk('local')->delete($private);

        return redirect()->route('admin.users.index')->with('status', 'Le compte de '.$name.' a été supprimé.');
    }
}
