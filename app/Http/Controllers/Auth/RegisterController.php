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
            'email' => ['nullable', 'email', 'max:120', 'unique:users,email'],
            'password' => ['required', Password::min(8)->numbers()],
            'terms' => ['accepted'],
        ], [
            'phone_full.unique' => 'Ce numéro a déjà un compte. Connectez-vous.',
            'phone_full.regex' => 'Numéro de téléphone invalide.',
            'terms.accepted' => 'Vous devez accepter les conditions d’utilisation.',
        ]);

        session(['register' => [
            'role' => $data['role'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'phone' => $data['phone_full'],
            'email' => $data['email'] ?? null,
            'password' => Hash::make($data['password']),
        ]]);

        $this->sendCode($data['phone_full']);

        return redirect()->route('register.verify');
    }

    /** Étape 2 : code SMS */
    public function verifyForm(): View|RedirectResponse
    {
        $pending = session('register');
        if (! $pending) {
            return redirect()->route('register');
        }

        return view('auth.verify', [
            'phone' => OtpService::format($pending['phone']),
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
        if (! $pending) {
            return redirect()->route('register');
        }

        $request->validate(['code' => ['required', 'digits:'.config('backtoburkina.otp.length')]]);

        $result = $this->otp->verify($pending['phone'], $request->input('code'));
        if ($result !== true) {
            return back()->withErrors(['code' => $result]);
        }

        $user = DB::transaction(function () use ($pending) {
            $user = User::create([
                ...$pending,
                'password' => $pending['password'], // déjà haché
                'phone_verified_at' => now(),
            ]);

            Conversation::superGroup()->users()->syncWithoutDetaching([$user->id => ['role' => 'member']]);

            if ($user->needsDossier()) {
                Dossier::openFor($user);
            }

            return $user;
        });

        session()->forget('register');
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

        if ($this->otp->secondsBeforeResend($pending['phone']) > 0) {
            return back()->withErrors(['code' => 'Patientez avant de demander un nouveau code.']);
        }

        $this->sendCode($pending['phone']);

        return back()->with('status', 'Un nouveau code a été envoyé.');
    }

    private function sendCode(string $phone): void
    {
        $code = $this->otp->send($phone);

        if (app()->isLocal() && config('backtoburkina.otp.show_in_dev')) {
            session()->flash('dev_code', $code);
        }
    }
}
