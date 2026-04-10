<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Client\DashboardController;
use App\Http\Controllers\Client\DocumentController;
use App\Http\Controllers\Client\MessageController;
use App\Http\Controllers\Client\ProfileController;
use App\Http\Controllers\Client\ProjectController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\MeController;
use App\Http\Controllers\PublicAvatarController;
use App\Http\Controllers\Site\BlogController;
use App\Http\Controllers\Site\StaticPageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Vitrine (vues Blade dans resources/views/vitrine)
|--------------------------------------------------------------------------
*/
Route::get('/', [StaticPageController::class, 'home'])->name('home');

Route::get('/qui-sommes-nous', [StaticPageController::class, 'show'])
    ->defaults('vitrinePage', 'about')
    ->name('vitrine.about');

Route::get('/nous-achetons', [StaticPageController::class, 'show'])
    ->defaults('vitrinePage', 'seller')
    ->name('vitrine.seller');

Route::get('/nous-vendons', [StaticPageController::class, 'show'])
    ->defaults('vitrinePage', 'buyer')
    ->name('vitrine.buyer');

Route::get('/investisseurs', [StaticPageController::class, 'show'])
    ->defaults('vitrinePage', 'investor')
    ->name('vitrine.investor');

Route::get('/mentions-legales', [StaticPageController::class, 'show'])
    ->defaults('vitrinePage', 'legals')
    ->name('vitrine.legals');

Route::get('/politique-cookies', [StaticPageController::class, 'show'])
    ->defaults('vitrinePage', 'cookies')
    ->name('vitrine.cookies');

Route::get('/contributeurs', [StaticPageController::class, 'show'])
    ->defaults('vitrinePage', 'contributors')
    ->name('vitrine.contributors');

Route::get('/simulation', [StaticPageController::class, 'show'])
    ->defaults('vitrinePage', 'simulation')
    ->name('vitrine.simulation');

Route::get('/nos-realisations', [StaticPageController::class, 'show'])
    ->defaults('vitrinePage', 'realizations')
    ->name('vitrine.realizations');

Route::permanentRedirect('/nos-realisation', '/nos-realisations');

Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])
    ->where('slug', '[a-z0-9]+(?:-[a-z0-9]+)*')
    ->name('blog.show');

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

Route::get('/avatars/{filename}', [PublicAvatarController::class, 'show'])
    ->where('filename', '[a-zA-Z0-9_-]{8,255}\.[a-zA-Z0-9]{2,8}')
    ->name('storage.avatar');

/*
|--------------------------------------------------------------------------
| Espace membre /me (tous rôles)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->prefix('me')->name('me.')->group(function () {
    Route::get('/', [MeController::class, 'show'])->name('index');
    Route::get('/edit', [MeController::class, 'edit'])->name('edit');
    Route::put('/edit', [MeController::class, 'update'])->name('update');
    Route::delete('/avatar', [MeController::class, 'destroyAvatar'])->name('avatar.destroy');
    Route::get('/password', [MeController::class, 'editPassword'])->name('password');
    Route::put('/password', [MeController::class, 'updatePassword'])->name('password.update');
});

Route::middleware(['auth', 'role:client,seller,admin,collaborator'])->prefix('client')->name('client.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/projet/{operation}', [ProjectController::class, 'show'])->name('project.show');
    Route::post('/document-request/{documentRequest}/upload', [DocumentController::class, 'store'])->name('document.upload');
    Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profil', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/projet/{operation}/message', [MessageController::class, 'store'])->name('message.store');
});
