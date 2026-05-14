<?php

use App\Models\User;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Marketing / Public Pages (Blade)
|--------------------------------------------------------------------------
*/

// Landing page
Route::view('/', 'marketing');          // homepage
Route::view('/landing', 'marketing');   // optional alias

// Public Blade pages
Route::view('/submit-job', 'submit-job')->name('submit.job');

/*
|--------------------------------------------------------------------------
| Vue SPA (Dashboard)
|--------------------------------------------------------------------------
*/

Route::view('/dashboard', 'app');
Route::view('/{any}', 'app')->where('any', '.*');

