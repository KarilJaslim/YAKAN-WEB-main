<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpFoundation\Response;

class RequireTwoFactor
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        if (!$user) {
            return $next($request);
        }

        // Check if user has 2FA enabled
        if ($user->two_factor_secret && $user->two_factor_confirmed_at) {
            // Check if 2FA is already verified in this session
            if (!session('2fa_verified')) {
                return Redirect::route('2fa.challenge');
            }
        }

        return $next($request);
    }
}
