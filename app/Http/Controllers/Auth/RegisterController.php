<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Dossier;
use App\Models\GroupInvitation;
use App\Models\User;
use App\Services\OtpService;
use App\Support\Phone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function __construct(private OtpService $otp) {}

    /** Écran « Je suis… » (choix du profil) */
    public function welcome(): View
    {
        return view('auth.welcome', ['roles' => User::PUBLIC_ROLES]);
    }

    /** Étape 1 : création du compte */
    public function create(Request $request): View
    {
        $role = $request->query('profil', session('register.role', User::ROLE_CANDIDATE));
        if (! array_key_exists($role, User::PUBLIC_ROLES)) {
            $role = User::ROLE_CANDIDATE;
        }

        return view('auth.register', [
            'role' => $role,
            'roles' => User::PUBLIC_ROLES,
            'old' => session('register', []),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'email' => OtpService::normalizeEmail((string) $request->input('email')),
            'phone_full' => Phone::normalize((string) $request->input('phone')),
        ]);

        $data = $request->validate([
            'role' => ['required', Rule::in(array_keys(User::PUBLIC_ROLES))],
            'first_name' => ['required', 'string', 'max:60'],
            'last_name' => ['required', 'string', 'max:60'],
            'email' => ['required', 'email', 'max:120', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:20'],
            'phone_full' => ['required', 'regex:/^\+\d{8,15}$/', 'unique:users,phone'],
            'password' => ['required', Password::min(8)->numbers()],
            'terms' => ['accepted'],
        ], [
            'email.required' => 'L’adresse e-mail est nécessaire pour créer votre compte.',
            'email.email' => 'Adresse e-mail invalide.',
            'email.unique' => 'Cette adresse e-mail a déjà un compte. Connectez-vous.',
            'phone_full.unique' => 'Ce numéro est déjà utilisé par un autre compte.',
            'phone_full.regex' => 'Numéro de téléphone invalide.',
            'terms.accepted' => 'Vous devez accepter les conditions d’utilisation.',
        ]);

        session(['register' => [
            'role' => $data['role'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone_full'],
            'password' => Hash::make($data['password']),
        ]]);

        if (! $this->sendCode($data['email'])) {
            return back()->withInput()->withErrors(['email' => 'Impossible d’envoyer le code à cette adresse pour le moment. Réessayez dans un instant.']);
        }

        return redirect()->route('register.verify');
    }

    /** Étape 2 : code reçu par e-mail */
    public function verifyForm(): View|RedirectResponse
    {
        $pending = session('register');
        if (! $pending) {
            return redirect()->route('register');
        }

        return view('auth.verify', [
            'title' => 'Vérifiez votre e-mail',
            'lead' => 'Pour créer votre compte, saisissez le code envoyé à',
            'destination' => OtpService::maskEmail($pending['email']),
            'action' => route('register.verify.store'),
            'resendAction' => route('register.resend'),
            'backUrl' => route('register'),
            'backLabel' => 'Modifier mon adresse',
            'step' => 2,
            'wait' => $this->otp->secondsBeforeResend($pending['email'], OtpService::REGISTER),
        ]);
    }

    public function verify(Request $request): RedirectResponse
    {
        $pending = session('register');
        if (! $pending) {
            return redirect()->route('register');
        }

        $request->validate(['code' => ['required', 'digits:'.config('backtoburkina.otp.length')]]);

        $result = $this->otp->verify($pending['email'], OtpService::REGISTER, $request->input('code'));
        if ($result !== true) {
            return back()->withErrors(['code' => $result]);
        }

        // L'adresse ou le numéro a pu être pris entre-temps
        if (User::where('email', $pending['email'])->orWhere('phone', $pending['phone'])->exists()) {
            session()->forget('register');

            return redirect()->route('login')->withErrors(['email' => 'Un compte existe déjà avec cette adresse ou ce numéro. Connectez-vous.']);
        }

        $user = DB::transaction(function () use ($pending) {
            $user = User::create([
                ...$pending,
                'password' => $pending['password'], // déjà haché
                'email_verified_at' => now(),
            ]);

            Conversation::superGroup()->users()->syncWithoutDetaching([$user->id => ['role' => 'member']]);

            if ($user->needsDossier()) {
                Dossier::openFor($user);
            }

            return $user;
        });

        session()->forget('register');

        // L'adresse vient d'être vérifiée par code : les invitations de groupe qui lui sont destinées sont acceptées
        GroupInvitation::acceptPendingFor($user);

        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->route('onboarding.profile');
    }

    public function resend(): RedirectResponse
    {
        $pending = session('register');
        if (! $pending) {
            return redirect()->route('register');
        }

        if ($this->otp->secondsBeforeResend($pending['email'], OtpService::REGISTER) > 0) {
            return back()->withErrors(['code' => 'Patientez avant de demander un nouveau code.']);
        }

        if (! $this->sendCode($pending['email'])) {
            return back()->withErrors(['code' => 'Impossible d’envoyer le code pour le moment. Réessayez dans un instant.']);
        }

        return back()->with('status', 'Un nouveau code vous a été envoyé par e-mail.');
    }

    private function sendCode(string $email): bool
    {
        try {
            $code = $this->otp->send($email, OtpService::REGISTER);
        } catch (\Throwable $e) {
            report($e);

            return false;
        }

        if (app()->isLocal() && config('backtoburkina.otp.show_in_dev')) {
            session()->flash('dev_code', $code);
        }

        return true;
    }
}
