<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AdminCheck
{
    public function handle($request, Closure $next)
    {
        // Check if authenticated on admin guard
        if (Auth::guard('admin')->check()) {
            $user = Auth::guard('admin')->user();
            
            // Verify user has admin role
            if ($user && $user->role === 'admin') {
                return $next($request);
            }
        }
        
        // If not authenticated as admin, log the attempt and redirect
        \Log::warning('Unauthorized admin access attempt', [
            'url' => $request->fullUrl(),
            'admin_guard' => Auth::guard('admin')->check(),
            'web_guard' => Auth::guard('web')->check(),
            'admin_user' => Auth::guard('admin')->user() ? Auth::guard('admin')->user()->email : null,
            'web_user' => Auth::guard('web')->user() ? Auth::guard('web')->user()->email : null,
        ]);
        
        // Store intended URL for redirect after login
        session(['url.intended' => $request->url()]);
        
        // Redirect to admin login
        return redirect()->route('admin.login.form')->with('error', 'Please login as admin to continue');
    }
}
