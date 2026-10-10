<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Renvoie vers les étapes 3-4 de l'inscription tant qu'elles ne sont pas terminées. */
class EnsureOnboarded
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ! $user->onboarded_at && ! $user->isStaff()) {
            return redirect()->route('onboarding.profile');
        }

        return $next($request);
    }
}
