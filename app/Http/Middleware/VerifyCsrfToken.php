<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    protected $except = [
        'test-generate-pattern',
        'test-pattern-status/*',
        '/test-generate-pattern',
        '/test-pattern-status/*',
    ];
}
