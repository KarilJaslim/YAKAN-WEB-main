<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PreventRequestsDuringMaintenance
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // If app is in maintenance mode
        if (app()->isDownForMaintenance()) {
            abort(503, 'The application is in maintenance mode.');
        }

        return $next($request);
    }
}
