<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AccessibilityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Add accessibility headers
        $response->headers->set('Accessibility-Mode', 'enabled');
        
        return $response;
    }
}
