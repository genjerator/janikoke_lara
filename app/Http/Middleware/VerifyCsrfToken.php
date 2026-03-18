<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'auth/register',
        'auth/login',
        'auth/logout',
        'auth/forgot-password',
        'auth/forgot-password/send-new',
        'auth/reset-password',
//        'auth/verify-email/*',
//        'auth/email/verification-notification',
//        'profile',
    ];
}
