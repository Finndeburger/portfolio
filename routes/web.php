<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasskeyController;
use App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\Route;

// Home page
Route::get('/', [SiteController::class, 'index'])->name('home');

// Search results
Route::get('/results', [SiteController::class, 'results'])->name('results');

// API endpoint (JSON)
Route::get('/api/sites', [SiteController::class, 'apiIndex'])->name('api.sites.index');

Route::prefix('auth')->name('auth.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
        Route::post('/passkey/login/options', [PasskeyController::class, 'loginOptions'])->name('passkey.login.options');
        Route::post('/passkey/login/verify', [PasskeyController::class, 'loginVerify'])->name('passkey.login.verify');
        Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
        Route::post('/register', [AuthController::class, 'register'])->name('register.store');
        Route::get('/password-suggestion', [AuthController::class, 'passwordSuggestion'])->name('password.suggestion');
        Route::get('/fake-email-suggestion', [AuthController::class, 'fakeEmailSuggestion'])->name('email.suggestion');
    });

    Route::middleware('auth')->group(function () {
        Route::get('/passkey', [AuthController::class, 'passkey'])->name('passkey');
        Route::post('/passkey/register/options', [PasskeyController::class, 'registerOptions'])->name('passkey.register.options');
        Route::post('/passkey/register/verify', [PasskeyController::class, 'registerVerify'])->name('passkey.register.verify');
        Route::delete('/passkey/credential', [PasskeyController::class, 'deleteCredential'])->name('passkey.credential.delete');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    });
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::delete('/users', [AdminController::class, 'destroyAllUsers'])->name('users.destroy-all');
    Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');
    Route::put('/users/{user}/role', [AdminController::class, 'updateUserRole'])->name('users.role');
    Route::post('/users/{user}/reset-password', [AdminController::class, 'resetUserPassword'])->name('users.reset-password');

    Route::get('/sites', [AdminController::class, 'sites'])->name('sites');
    Route::get('/sites/create', [AdminController::class, 'siteCreate'])->name('sites.create');
    Route::post('/sites/create', [AdminController::class, 'storeSiteCreate'])->name('sites.create.store');
    Route::get('/sites/{site}/info', [AdminController::class, 'siteInfo'])->name('sites.info');
    Route::put('/sites/{site}/info', [AdminController::class, 'updateSiteInfo'])->name('sites.info.update');
});

// Mini site routes:
// - /sites/{slug} resolves to pages/sites/{slug}/index.blade.php
// - /sites/{slug}/{page} resolves to pages/sites/{slug}/{page}.blade.php
// Only the root mini site entry is searchable via the `sites` table.
Route::get('/sites/{slug}/{page?}', [SiteController::class, 'show'])
    ->where('slug', '[A-Za-z0-9\-]+')
    ->where('page', '.*')
    ->name('sites.show');
