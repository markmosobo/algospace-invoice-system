<?php

use App\Models\User;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CyberRequestController;

/*
|--------------------------------------------------------------------------
| Marketing / Public Pages (Blade)
|--------------------------------------------------------------------------
*/

// Landing page
Route::view('/', 'marketing');          // homepage
Route::view('/landing', 'marketing');   // optional alias

// Public Blade pages
Route::get('/submit-job', [CyberRequestController::class, 'create'])
    ->name('submit.job');
Route::post('/cyber-request', [CyberRequestController::class, 'store'])
    ->name('cyber.requests.store');
/*
|--------------------------------------------------------------------------
| Vue SPA (Dashboard)
|--------------------------------------------------------------------------
*/

Route::view('/dashboard', 'app');
Route::view('/{any}', 'app')->where('any', '.*');

