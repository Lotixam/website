<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Client\DashboardController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\Site\StaticPageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Vitrine (fichiers HTML dans public/html)
|--------------------------------------------------------------------------
*/
Route::get('/', [StaticPageController::class, 'home'])->name('home');

Route::get('/qui-sommes-nous', [StaticPageController::class, 'show'])
    ->defaults('vitrinePage', 'about.html')
    ->name('vitrine.about');

Route::get('/nous-achetons', [StaticPageController::class, 'show'])
    ->defaults('vitrinePage', 'seller.html')
    ->name('vitrine.seller');

Route::get('/nous-vendons', [StaticPageController::class, 'show'])
    ->defaults('vitrinePage', 'buyer.html')
    ->name('vitrine.buyer');

Route::get('/investisseurs', [StaticPageController::class, 'show'])
    ->defaults('vitrinePage', 'investor.html')
    ->name('vitrine.investor');

Route::get('/mentions-legales', [StaticPageController::class, 'show'])
    ->defaults('vitrinePage', 'legals.html')
    ->name('vitrine.legals');

Route::get('/contributeurs', [StaticPageController::class, 'show'])
    ->defaults('vitrinePage', 'contributors.html')
    ->name('vitrine.contributors');

Route::get('/simulation', [StaticPageController::class, 'show'])
    ->defaults('vitrinePage', 'simulation.html')
    ->name('vitrine.simulation');

/*
|--------------------------------------------------------------------------
| Anciennes URLs (SEO / liens externes)
|--------------------------------------------------------------------------
*/
Route::permanentRedirect('/html/index.html', '/');
Route::permanentRedirect('/html/about.html', '/qui-sommes-nous');
Route::permanentRedirect('/html/seller.html', '/nous-achetons');
Route::permanentRedirect('/html/buyer.html', '/nous-vendons');
Route::permanentRedirect('/html/investor.html', '/investisseurs');
Route::permanentRedirect('/html/legals.html', '/mentions-legales');
Route::permanentRedirect('/html/contributors.html', '/contributeurs');
Route::permanentRedirect('/html/simulation.html', '/simulation');

/*
|--------------------------------------------------------------------------
| Auth, contact, espace client
|--------------------------------------------------------------------------
*/
Route::get('/login', [LoginController::class, 'create'])->middleware('guest')->name('login');
Route::post('/login', [LoginController::class, 'store'])->middleware('guest');
Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

Route::get('/contact', [ContactController::class, 'create'])->name('contact');
Route::post('/contact', [ContactController::class, 'store']);

Route::get('/maintenance', [MaintenanceController::class, 'show'])->name('maintenance');

Route::middleware('auth')->prefix('client')->name('client.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
});
