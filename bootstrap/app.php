<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Add TrustProxies to handle reverse proxy headers
        $middleware->trustProxies(at: '*');
        
        // Add custom middleware
        $middleware->web([\App\Http\Middleware\EnsureRoleSession::class,]);
        $middleware->alias([
            'role'  => \App\Http\Middleware\CheckRole::class,
        ]);

        // Throttle groups (file-based, no Redis required):
        // Default 'throttle' = 60 req/min for normal web routes
        // 'throttle:300,1'  = 300 req/min for admin/bulk operations (applied per-route in web.php)
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
