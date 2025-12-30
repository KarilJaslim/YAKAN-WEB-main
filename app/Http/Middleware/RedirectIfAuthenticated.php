<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // If this is an admin guard and user is accessing admin login, redirect to admin dashboard
                if ($guard === 'admin' && $request->is('admin/*')) {
                    return redirect('/admin/dashboard');
                }
                // If this is an admin guard but not accessing admin routes, still redirect to admin dashboard
                elseif ($guard === 'admin') {
                    return redirect('/admin/dashboard');
                }
                // For web guard or default, redirect to user dashboard
                return redirect('/dashboard');
            }
        }

        return $next($request);
    }
}
