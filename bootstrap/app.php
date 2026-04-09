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
    ->withMiddleware(function (Middleware $middleware): void {
        // SSL terminé devant (LWS, CDN) : X-Forwarded-Proto pour que la requête soit vue en HTTPS.
        $middleware->trustProxies(at: '*');
        $middleware->redirectUsersTo(function () {
            $user = auth()->user();
            if ($user && ($user->hasRole('admin') || $user->hasRole('collaborator'))) {
                return route('filament.admin.pages.dashboard');
            }
            return route('client.dashboard');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
