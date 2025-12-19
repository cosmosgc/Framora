<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (
            !$user ||
            !(
                $user->id === 1 ||
                $user->hasRole('host') ||
                $user->hasRole('admin')
            )
        ) {
            abort(403, 'Unauthorized admin access.');
        }

        return $next($request);
    }
}
