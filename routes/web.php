
<?php

  

use App\Http\Controllers\SiteController;

use Illuminate\Support\Facades\Route;

  

// Home page

Route::get('/', [SiteController::class, 'index']);

  

// Search results

Route::get('/results', [SiteController::class, 'results']);

  

// API endpoint (JSON)

Route::get('/api/sites', [SiteController::class, 'apiIndex']);

  

// Custom site pages (defined before the dynamic {slug} route so they take priority)

Route::get('/sites/about', fn () => view('pages.sites.about'));

Route::get('/sites/test', fn () => view('pages.sites.test'));

  

// Dynamic site page — catches any /sites/whatever that isn't about or test

Route::get('/sites/{slug}', [SiteController::class, 'show']);