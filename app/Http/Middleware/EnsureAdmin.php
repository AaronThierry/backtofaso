<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Gestion des utilisateurs et des rôles : réservée aux administrateurs */
class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless($request->user()?->role === User::ROLE_ADMIN, 403, 'Action réservée aux administrateurs.');

        return $next($request);
    }
}
