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


/*
|--------------------------------------------------------------------------
| Vue SPA (Dashboard)
|--------------------------------------------------------------------------
*/

Route::view('/dashboard', 'app');
Route::view('/{any}', 'app')->where('any', '.*');

