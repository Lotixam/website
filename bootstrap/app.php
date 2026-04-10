<?php

use App\Http\Middleware\EnsureUserHasRole;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
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
        $middleware->alias([
            'role' => EnsureUserHasRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Livewire (Filament) envoie X-Livewire sans Accept: application/json ; sans ceci,
        // une 419/500 renvoie du HTML et le navigateur lève « Unexpected end of JSON input ».
        $exceptions->shouldRenderJsonWhen(
            fn ($request, Throwable $e) => $request->is('api/*')
                || $request->hasHeader('X-Livewire')
                || $request->expectsJson()
        );
    })->create();
