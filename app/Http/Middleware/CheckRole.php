<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  array|string  $roles  Role(s) yang boleh akses
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Cek login
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'You must be logged in to access this page.');
        }

        $user = Auth::user();
       


        // Cek role
        if (!in_array($user->role_name, $roles)) {
            return redirect()->route('homes')->with('error', 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}
