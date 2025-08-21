<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role)
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        $user = Auth::user();
        
        // Check if user has the required role
        if (!$user->hasRole($role)) {
            // Optionally, you can redirect to a specific page or return a 403 error
            return redirect('home')->with('error', 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}
