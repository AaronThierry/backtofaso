<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

/**
 * Connexion en deux temps : e-mail + mot de passe, puis code reçu par e-mail.
 * La session n'est ouverte qu'après la saisie du code.
 */
class LoginController extends Controller
{
    /** Durée pendant laquelle le code de connexion peut être saisi après le mot de passe */
    private const PENDING_MINUTES = 15;

    public function __construct(private OtpService $otp) {}

    public function create(): View
    {
        return view('auth.login');
    }

    /** Étape 1 : vérification de l'e-mail et du mot de passe, puis envoi du code */
    public function store(Request $request): RedirectResponse
    {
        $request->merge(['email' => OtpService::normalizeEmail((string) $request->input('email'))]);
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Saisissez votre adresse e-mail.',
            'email.email' => 'Adresse e-mail invalide.',
        ]);

        $user = User::where('email', $data['email'])->first();
        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'E-mail ou mot de passe incorrect.']);
        }

        if (! $this->sendCode($user->email)) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'Impossible d’envoyer le code de connexion pour le moment. Réessayez dans un instant.']);
        }

        session(['login_pending' => ['id' => $user->id, 'email' => $user->email, 'until' => now()->addMinutes(self::PENDING_MINUTES)->timestamp]]);

        return redirect()->route('login.verify');
    }

    /** Étape 2 : saisie du code */
    public function verifyForm(): View|RedirectResponse
    {
        $pending = $this->pending();
        if (! $pending) {
            return redirect()->route('login')->withErrors(['email' => 'Votre session a expiré. Reconnectez-vous.']);
        }

        return view('auth.verify', [
            'title' => 'Confirmez votre connexion',
            'lead' => 'Par sécurité, saisissez le code de connexion envoyé à',
            'destination' => OtpService::maskEmail($pending['email']),
            'action' => route('login.verify.store'),
            'resendAction' => route('login.resend'),
            'backUrl' => route('login'),
            'backLabel' => 'Changer de compte',
            'step' => null,
            'wait' => $this->otp->secondsBeforeResend($pending['email'], OtpService::LOGIN),
        ]);
    }

    public function verify(Request $request): RedirectResponse
    {
        $pending = $this->pending();
        if (! $pending) {
            return redirect()->route('login')->withErrors(['email' => 'Votre session a expiré. Reconnectez-vous.']);
        }
        $request->validate(['code' => ['required', 'digits:'.config('backtoburkina.otp.length')]]);

        $result = $this->otp->verify($pending['email'], OtpService::LOGIN, $request->input('code'));
        if ($result !== true) {
            return back()->withErrors(['code' => $result]);
        }

        $user = User::whereKey($pending['id'])->where('email', $pending['email'])->first();
        if (! $user) {
            session()->forget('login_pending');

            return redirect()->route('login')->withErrors(['email' => 'Compte introuvable. Reconnectez-vous.']);
        }

        // Le code reçu prouve aussi que l'adresse fonctionne
        if (! $user->email_verified_at) {
            $user->forceFill(['email_verified_at' => now()])->save();
        }

        session()->forget('login_pending');
        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function resend(): RedirectResponse
    {
        $pending = $this->pending();
        if (! $pending) {
            return redirect()->route('login');
        }

        if ($this->otp->secondsBeforeResend($pending['email'], OtpService::LOGIN) > 0) {
            return back()->withErrors(['code' => 'Patientez avant de demander un nouveau code.']);
        }

        if (! $this->sendCode($pending['email'])) {
            return back()->withErrors(['code' => 'Impossible d’envoyer le code pour le moment. Réessayez dans un instant.']);
        }

        return back()->with('status', 'Un nouveau code vous a été envoyé par e-mail.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    /** Connexion en attente du code, ou null si absente ou expirée */
    private function pending(): ?array
    {
        $pending = session('login_pending');
        if (! $pending || $pending['until'] < now()->timestamp) {
            session()->forget('login_pending');

            return null;
        }

        return $pending;
    }

    private function sendCode(string $email): bool
    {
        try {
            $code = $this->otp->send($email, OtpService::LOGIN);
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
