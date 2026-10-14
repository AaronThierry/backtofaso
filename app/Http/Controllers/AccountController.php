<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\Destinations;
use App\Support\Phone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

/** Mon compte : informations, photo, notifications, mot de passe, suppression */
class AccountController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user();

        return view('app.account', [
            'user' => $user,
            'phone' => Phone::format($user->phone),
            'cities' => config('backtoburkina.cities'),
            'destinations' => Destinations::options(),
            'levels' => User::LANGUAGE_LEVELS,
        ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = $request->user();
        $request->merge(['phone_full' => Phone::normalize((string) $request->input('phone'))]);

        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:60'],
            'last_name' => ['required', 'string', 'max:60'],
            'phone' => ['required', 'string', 'max:20'],
            'phone_full' => ['required', 'regex:/^\+\d{8,15}$/', Rule::unique('users', 'phone')->ignore($user->id)],
            'city' => ['nullable', 'string', 'max:60'],
            'birth_date' => ['nullable', 'date', 'before:-14 years'],
            'destination' => ['nullable', Rule::in(array_keys(Destinations::options()))],
            'language_level' => ['nullable', Rule::in(array_keys(User::LANGUAGE_LEVELS))],
        ], [
            'phone_full.unique' => 'Ce numéro est déjà utilisé par un autre compte.',
            'phone_full.regex' => 'Numéro de téléphone invalide.',
            'birth_date.before' => 'Date de naissance invalide.',
        ]);

        $data['phone'] = $data['phone_full'];
        unset($data['phone_full']);
        $user->update($data);

        return redirect()->to(route('account').'#informations')->with('status', 'Vos informations ont été enregistrées.');
    }

    public function updatePhoto(Request $request): RedirectResponse
    {
        $request->validate(['avatar' => ['required', 'image', 'max:4096']], [
            'avatar.image' => 'Choisissez une image (JPG, PNG ou WebP).',
            'avatar.max' => 'L’image ne doit pas dépasser 4 Mo.',
        ]);

        $user = $request->user();
        $old = $user->avatar_path;
        $user->update(['avatar_path' => $request->file('avatar')->store('avatars', 'public')]);
        if ($old) {
            Storage::disk('public')->delete($old);
        }

        return redirect()->route('account')->with('status', 'Votre photo a été mise à jour.');
    }

    public function deletePhoto(Request $request): RedirectResponse
    {
        $user = $request->user();
        if ($user->avatar_path) {
            Storage::disk('public')->delete($user->avatar_path);
            $user->update(['avatar_path' => null]);
        }

        return redirect()->route('account')->with('status', 'Votre photo a été retirée.');
    }

    public function updateNotifications(Request $request): RedirectResponse
    {
        $request->user()->update([
            'notify_push' => $request->boolean('notify_push'),
            'notify_sms' => $request->boolean('notify_sms'),
            'notify_whatsapp' => $request->boolean('notify_whatsapp'),
            'notify_email' => $request->boolean('notify_email'),
        ]);

        return redirect()->to(route('account').'#notifications')->with('status', 'Vos préférences de notification ont été enregistrées.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validateWithBag('password', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'different:current_password', Password::min(8)->numbers()],
        ], [
            'current_password.current_password' => 'Le mot de passe actuel est incorrect.',
            'password.confirmed' => 'Les deux nouveaux mots de passe ne correspondent pas.',
            'password.different' => 'Choisissez un mot de passe différent de l’actuel.',
        ]);

        $request->user()->update(['password' => Hash::make($request->input('password'))]);

        return redirect()->to(route('account').'#securite')->with('status', 'Votre mot de passe a été changé.');
    }

    /** Suppression définitive du compte et des fichiers déposés */
    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_if($user->isStaff(), 403, 'Les comptes de l’équipe ne peuvent pas être supprimés ici.');
        $request->merge(['confirmation' => mb_strtoupper(trim((string) $request->input('confirmation')))]);

        $request->validateWithBag('deletion', [
            'password' => ['required', 'current_password'],
            'confirmation' => ['required', 'in:SUPPRIMER'],
        ], [
            'password.current_password' => 'Mot de passe incorrect.',
            'confirmation.in' => 'Tapez SUPPRIMER en majuscules pour confirmer.',
        ]);

        $files = array_filter([
            $user->avatar_path,
            $user->talentProfile?->cv_path,
            ...$user->messages()->whereNotNull('attachment_path')->pluck('attachment_path')->all(),
        ]);
        $private = $user->dossier?->documents()->whereNotNull('path')->pluck('path')->all() ?? [];

        Auth::logout();
        $user->delete();
        Storage::disk('public')->delete($files);
        Storage::disk('local')->delete($private);

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('status', 'Votre compte a été supprimé. Merci d’avoir utilisé Back to Burkina.');
    }
}
