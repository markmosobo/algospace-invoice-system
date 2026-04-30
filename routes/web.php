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

/*
|--------------------------------------------------------------------------
| EMAIL VERIFICATION (FIXED FOR API + VUE)
|--------------------------------------------------------------------------
*/

Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {

    $user = User::findOrFail($id);

    // verify hash manually
    if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
        abort(403, 'Invalid verification link');
    }

    // mark verified
    if (! $user->hasVerifiedEmail()) {
        $user->email_verified_at = now();
        $user->save();
    }

    return redirect(env('FRONTEND_URL') . '/login?verified=1');

})->middleware(['signed'])->name('verification.verify');