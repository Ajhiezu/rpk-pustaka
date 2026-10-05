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
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!$request->user()) {
            abort(403, 'Unauthorized access.');
        }

        // Map 'member' to 'anggota' for backward compatibility
        $normalizedRoles = array_map(fn($r) => $r === 'member' ? 'anggota' : $r, $roles);

        if (!in_array($request->user()->role, $normalizedRoles)) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}
