<?php

use App\Models\User;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CyberRequestController;
use App\Http\Controllers\MarketingController;

/*
|--------------------------------------------------------------------------
| Marketing / Public Pages (Blade)
|--------------------------------------------------------------------------
*/

// Landing page
Route::get('/', [MarketingController::class, 'index']);
Route::get('/landing', [MarketingController::class, 'index']);

// Public Blade pages
Route::get('/submit-job', [CyberRequestController::class, 'create'])
    ->name('submit.job');
Route::post('/cyber-request', [CyberRequestController::class, 'store'])
    ->name('cyber.requests.store');
Route::get('/contact', [MarketingController::class, 'contact']);
Route::get('/about', [MarketingController::class, 'about']);
Route::get('/services/category/{category}', [MarketingController::class, 'byCategory'])
    ->name('services.byCategory');
Route::get('/services/{id}', [MarketingController::class, 'showService']);    
/*
|--------------------------------------------------------------------------
| Vue SPA (Dashboard)
|--------------------------------------------------------------------------
*/

Route::view('/dashboard', 'app');
Route::view('/{any}', 'app')->where('any', '.*');

