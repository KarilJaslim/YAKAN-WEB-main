<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class RateLimit
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!env('RATE_LIMITING_ENABLED', true)) {
            return $next($request);
        }

        $key = 'api:' . $request->ip() . ':' . $request->route()->getName();
        $maxAttempts = env('API_RATE_LIMIT', 60);
        $decayMinutes = env('API_RATE_LIMIT_WINDOW', 1);

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            
            return response()->json([
                'success' => false,
                'message' => 'Too many attempts. Please try again later.',
                'retry_after' => $seconds
            ], 429);
        }

        RateLimiter::hit($key, $decayMinutes * 60);

        return $next($request);
    }
}
