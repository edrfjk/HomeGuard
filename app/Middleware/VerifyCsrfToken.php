<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * All /api/* routes are stateless (ESP32 devices), so they
     * must be excluded from CSRF protection.
     */
    protected $except = [
        'api/*',
    ];
}
