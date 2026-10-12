<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Dossier;
use App\Models\User;
use App\Services\OtpService;
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
        $request->merge(['phone_full' => OtpService::normalize((string) $request->input('phone'))]);

        $data = $request->validate([
            'role' => ['required', Rule::in(array_keys(User::PUBLIC_ROLES))],
            'first_name' => ['required', 'string', 'max:60'],
            'last_name' => ['required', 'string', 'max:60'],
            'phone' => ['required', 'string', 'max:20'],
            'phone_full' => ['required', 'regex:/^\+\d{8,15}$/', 'unique:users,phone'],
            'email' => [OtpService::usesMail() ? 'required' : 'nullable', 'email', 'max:120', 'unique:users,email'],
            'password' => ['required', Password::min(8)->numbers()],
            'terms' => ['accepted'],
        ], [
            'phone_full.unique' => 'Ce numéro a déjà un compte. Connectez-vous.',
            'email.unique' => 'Cette adresse e-mail a déjà un compte. Connectez-vous.',
            'email.required' => 'L’adresse e-mail est nécessaire pour recevoir votre code.',
            'phone_full.regex' => 'Numéro de téléphone invalide.',
            'terms.accepted' => 'Vous devez accepter les conditions d’utilisation.',
        ]);

        $pending = [
            'role' => $data['role'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'phone' => $data['phone_full'],
            'email' => $data['email'] ?? null,
            'password' => Hash::make($data['password']),
        ];

        // Vérification par code désactivée : le compte est créé directement
        if (! OtpService::enabled()) {
            return $this->completeRegistration($request, $pending, verified: false);
        }

        session(['register' => $pending]);

        if (! $this->sendCode($data['phone_full'], $data['email'] ?? null)) {
            return back()->withInput()->withErrors(['email' => 'Impossible d’envoyer le code pour le moment. Réessayez dans un instant.']);
        }

        return redirect()->route('register.verify');
    }

    /** Étape 2 : code SMS */
    public function verifyForm(): View|RedirectResponse
    {
        $pending = session('register');
        if (! $pending || ! OtpService::enabled()) {
            return redirect()->route('register');
        }

        $byMail = OtpService::channelFor($pending['email']) === 'mail';

        return view('auth.verify', [
            'via' => $byMail ? 'par e-mail à' : 'par SMS au',
            'destination' => $byMail ? OtpService::maskEmail($pending['email']) : OtpService::format($pending['phone']),
            'action' => route('register.verify.store'),
            'resendAction' => route('register.resend'),
            'backUrl' => route('register'),
            'step' => 2,
            'wait' => $this->otp->secondsBeforeResend($pending['phone']),
        ]);
    }

    public function verify(Request $request): RedirectResponse
    {
        $pending = session('register');
        if (! $pending || ! OtpService::enabled()) {
            return redirect()->route('register');
        }

        $request->validate(['code' => ['required', 'digits:'.config('backtoburkina.otp.length')]]);

        $result = $this->otp->verify($pending['phone'], $request->input('code'));
        if ($result !== true) {
            return back()->withErrors(['code' => $result]);
        }

        session()->forget('register');

        return $this->completeRegistration($request, $pending, verified: true);
    }

    /** Crée le compte, l'ajoute au super-groupe, ouvre son dossier et le connecte */
    private function completeRegistration(Request $request, array $pending, bool $verified): RedirectResponse
    {
        $user = DB::transaction(function () use ($pending, $verified) {
            $user = User::create([
                ...$pending,
                'password' => $pending['password'], // déjà haché
                'phone_verified_at' => $verified ? now() : null,
            ]);

            Conversation::superGroup()->users()->syncWithoutDetaching([$user->id => ['role' => 'member']]);

            if ($user->needsDossier()) {
                Dossier::openFor($user);
            }

            return $user;
        });

        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->route('onboarding.profile');
    }

    public function resend(): RedirectResponse
    {
        $pending = session('register');
        if (! $pending || ! OtpService::enabled()) {
            return redirect()->route('register');
        }

        if ($this->otp->secondsBeforeResend($pending['phone']) > 0) {
            return back()->withErrors(['code' => 'Patientez avant de demander un nouveau code.']);
        }

        if (! $this->sendCode($pending['phone'], $pending['email'])) {
            return back()->withErrors(['code' => 'Impossible d’envoyer le code pour le moment. Réessayez dans un instant.']);
        }

        return back()->with('status', 'Un nouveau code a été envoyé.');
    }

    private function sendCode(string $phone, ?string $email): bool
    {
        try {
            $code = $this->otp->send($phone, $email);
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
