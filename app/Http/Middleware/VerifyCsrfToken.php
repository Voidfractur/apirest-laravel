<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        //
        'http://apirest-laravel.com/registro',
        'http://apirest-laravel.com/libros',
        'http://apirest-laravel.com/libros/*'
    ];
}
