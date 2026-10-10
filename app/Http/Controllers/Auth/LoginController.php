<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function __construct(private OtpService $otp) {}

    public function create(): View
    {
        return view('auth.login');
    }

    /** Connexion par téléphone (ou e-mail) + mot de passe */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $field = filter_var($data['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
        $value = $field === 'phone' ? OtpService::normalize($data['login']) : $data['login'];

        if (! Auth::attempt([$field => $value, 'password' => $data['password']], true)) {
            return back()->withInput($request->only('login'))
                ->withErrors(['login' => 'Identifiants incorrects.']);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    /** Connexion sans mot de passe : envoi d'un code par SMS */
    public function sendCode(Request $request): RedirectResponse
    {
        $request->validate(['login' => ['required', 'string']]);
        $phone = OtpService::normalize($request->input('login'));

        if (! User::where('phone', $phone)->exists()) {
            return back()->withInput()->withErrors(['login' => 'Aucun compte avec ce numéro.']);
        }

        $code = $this->otp->send($phone);
        session(['login_phone' => $phone]);
        if (app()->isLocal() && config('backtoburkina.otp.show_in_dev')) {
            session()->flash('dev_code', $code);
        }

        return redirect()->route('login.code.form');
    }

    public function codeForm(): View|RedirectResponse
    {
        $phone = session('login_phone');
        if (! $phone) {
            return redirect()->route('login');
        }

        return view('auth.verify', [
            'phone' => OtpService::format($phone),
            'action' => route('login.code.verify'),
            'resendAction' => null,
            'backUrl' => route('login'),
            'step' => null,
            'wait' => 0,
        ]);
    }

    public function verifyCode(Request $request): RedirectResponse
    {
        $phone = session('login_phone');
        if (! $phone) {
            return redirect()->route('login');
        }
        $request->validate(['code' => ['required', 'digits:'.config('backtoburkina.otp.length')]]);

        $result = $this->otp->verify($phone, $request->input('code'));
        if ($result !== true) {
            return back()->withErrors(['code' => $result]);
        }

        $user = User::where('phone', $phone)->firstOrFail();
        session()->forget('login_phone');
        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
