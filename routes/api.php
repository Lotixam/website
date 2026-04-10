<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\OperationController;
use App\Http\Controllers\Api\TokenController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::post('/auth/login', [AuthController::class, 'login'])
        ->middleware('throttle:api-login')
        ->name('api.v1.auth.login');

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::post('/auth/logout', [AuthController::class, 'logout'])->name('api.v1.auth.logout');
        Route::get('/me', [AuthController::class, 'me'])->name('api.v1.me');

        Route::get('/tokens', [TokenController::class, 'index'])->name('api.v1.tokens.index');
        Route::post('/tokens', [TokenController::class, 'store'])->name('api.v1.tokens.store');
        Route::delete('/tokens/{id}', [TokenController::class, 'destroy'])->name('api.v1.tokens.destroy');

        Route::get('/operations', [OperationController::class, 'index'])->name('api.v1.operations.index');
        Route::get('/operations/{operation}', [OperationController::class, 'show'])->name('api.v1.operations.show');

        Route::get('/events', [EventController::class, 'index'])->name('api.v1.events.index');
        Route::get('/events/{event}', [EventController::class, 'show'])->name('api.v1.events.show');

        Route::middleware('role:admin')->group(function (): void {
            Route::get('/users', [UserController::class, 'index'])->name('api.v1.users.index');
            Route::get('/users/{user}', [UserController::class, 'show'])->name('api.v1.users.show');
        });
    });
});
