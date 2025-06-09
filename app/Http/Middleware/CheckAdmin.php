<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(!Auth::check()) {
            return redirect()->route('login')->with('error', 'You must be logged in to access this page.');
        } else {
            if(Auth::user()->is_admin == 0) {
                return redirect()->to('homes')->with('error', 'You do not have permission to access this page.');
            } else {
                return $next($request);
            }
        }
        
    }
}
