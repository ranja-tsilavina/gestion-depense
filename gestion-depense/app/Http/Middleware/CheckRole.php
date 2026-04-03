<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!auth()->check()) {
            return redirect('login');
        }

        $user = auth()->user();
        
        if ($role === 'owner' && !$user->isOwner()) {
            return redirect('dashboard')->with('error', 'Accès refusé. Cette action est réservée au propriétaire de la maison.');
        }

        if ($role === 'member' && !$user->isMember() && !$user->isOwner()) {
            return redirect('dashboard')->with('error', 'Accès refusé.');
        }

        return $next($request);
    }
}
