<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Check if the authenticated user has the required role
        if ($request->user() && $request->user()->role !== $role) {
            // If they don't match, redirect them to their specific dashboard
            return redirect()->route('user.dashboard')->with('error', 'Unauthorized access.');
        }

        return $next($request);
    }
}