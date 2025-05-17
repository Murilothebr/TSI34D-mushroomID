<?php

namespace Config;

class App
{
    public static array $middlewareAliases = [
        'auth' => \App\Middleware\AuthenticateMiddleware::class,
        'admin' => \App\Middleware\AdminMiddleware::class
    ];
}
