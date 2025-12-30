<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiter;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class AccountLockout
{
    /**
     * The rate limiter instance.
     */
    protected $limiter;

    /**
     * Create a new account lockout middleware.
     */
    public function __construct(RateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $maxAttempts = '5', string $decayMinutes = '15'): Response
    {
        $email = strtolower($request->input('email', ''));
        $key = 'login:attempts:' . $email;

        if ($this->limiter->tooManyAttempts($key, $maxAttempts)) {
            $this->logLockout($email, $request->ip());
            return $this->buildLockoutResponse($key, $maxAttempts);
        }

        $response = $next($request);

        // If authentication failed, increment attempts
        if ($response->getStatusCode() === 302 && $request->session()->has('errors')) {
            $this->limiter->hit($key, $decayMinutes * 60);
            $this->logFailedAttempt($email, $request->ip());
        } elseif ($response->getStatusCode() === 302) {
            // Successful login - clear attempts
            $this->limiter->clear($key);
            $this->logSuccessfulLogin($email, $request->ip());
        }

        return $response;
    }

    /**
     * Create a lockout response.
     */
    protected function buildLockoutResponse(string $key, int $maxAttempts): Response
    {
        $seconds = $this->limiter->availableIn($key);
        $minutes = ceil($seconds / 60);

        return back()
            ->withInput($request->only('email'))
            ->withErrors([
                'email' => "Too many failed login attempts. Your account has been temporarily locked. Please try again in {$minutes} minutes."
            ]);
    }

    /**
     * Log failed login attempt.
     */
    protected function logFailedAttempt(string $email, string $ip): void
    {
        \Log::warning('Failed login attempt', [
            'email' => $email,
            'ip' => $ip,
            'user_agent' => request()->userAgent(),
            'timestamp' => now()
        ]);
    }

    /**
     * Log account lockout.
     */
    protected function logLockout(string $email, string $ip): void
    {
        \Log::alert('Account locked due to too many failed attempts', [
            'email' => $email,
            'ip' => $ip,
            'user_agent' => request()->userAgent(),
            'timestamp' => now()
        ]);
    }

    /**
     * Log successful login.
     */
    protected function logSuccessfulLogin(string $email, string $ip): void
    {
        \Log::info('Successful login after failed attempts', [
            'email' => $email,
            'ip' => $ip,
            'timestamp' => now()
        ]);
    }
}
