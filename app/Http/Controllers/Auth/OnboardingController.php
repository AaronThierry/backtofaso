<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\TalentProfile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/** Étapes 3 (profil) et 4 (photo, notifications) de l'inscription */
class OnboardingController extends Controller
{
    public function profile(Request $request): View
    {
        return view('auth.onboarding-profile', [
            'user' => $request->user(),
            'degrees' => User::DEGREES,
            'fields' => User::FIELDS,
            'levels' => User::CHINESE_LEVELS,
            'cities' => config('backtoburkina.cities'),
        ]);
    }

    public function saveProfile(Request $request): RedirectResponse
    {
        if ($request->boolean('skip')) {
            return redirect()->route('onboarding.preferences');
        }

        $data = $request->validate([
            'birth_date' => ['nullable', 'date', 'before:-14 years'],
            'city' => ['nullable', 'string', 'max:60'],
            'last_degree' => ['nullable', Rule::in(User::DEGREES)],
            'interests' => ['nullable', 'array'],
            'interests.*' => [Rule::in(User::FIELDS)],
            'chinese_level' => ['nullable', Rule::in(array_keys(User::CHINESE_LEVELS))],
        ], ['birth_date.before' => 'Date de naissance invalide.']);

        $request->user()->update($data);

        return redirect()->route('onboarding.preferences');
    }

    public function preferences(Request $request): View
    {
        return view('auth.onboarding-preferences', ['user' => $request->user()]);
    }

    public function savePreferences(Request $request): RedirectResponse
    {
        $request->validate([
            'avatar' => ['nullable', 'image', 'max:4096'],
        ]);

        $user = $request->user();
        $attrs = [
            'notify_push' => $request->boolean('notify_push'),
            'notify_sms' => $request->boolean('notify_sms'),
            'notify_whatsapp' => $request->boolean('notify_whatsapp'),
            'notify_email' => $request->boolean('notify_email'),
            'onboarded_at' => $user->onboarded_at ?? now(),
        ];

        if ($request->hasFile('avatar')) {
            $attrs['avatar_path'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($attrs);

        // Un diplômé de retour obtient un profil talent à compléter (non publié tant qu'il n'est pas vérifié)
        if ($user->role === User::ROLE_GRADUATE && ! $user->talentProfile) {
            TalentProfile::create([
                'user_id' => $user->id,
                'headline' => 'Diplômé(e) de Chine',
                'field' => $user->interests[0] ?? 'Autre',
                'languages' => ['Français'],
                'location' => $user->city,
            ]);
        }

        return redirect()->route('onboarding.done');
    }

    public function done(Request $request): View
    {
        return view('auth.onboarding-done', ['user' => $request->user()]);
    }
}
