<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Back-office : réservé à l'équipe Travel Express (conseillers et administrateurs) */
class EnsureStaff
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless($request->user()?->isStaff(), 403, 'Espace réservé à l’équipe Travel Express.');

        return $next($request);
    }
}
