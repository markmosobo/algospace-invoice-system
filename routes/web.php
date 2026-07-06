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
Route::get('/library', [MarketingController::class, 'library']);
Route::get('/physical-books', [MarketingController::class, 'physicalBooks'])->name('physical.books');
Route::get('/e-books', [MarketingController::class, 'eBooks'])->name('e.books');
Route::get('/community-space', [MarketingController::class, 'communitySpace'])->name('community.space');

// Public training pages
Route::get('/training-courses', [MarketingController::class, 'trainingCourses']);
Route::get('/training-courses/schedule', [MarketingController::class, 'schedule']);
Route::get(
    '/training-courses/{course}',
    [MarketingController::class, 'showCourse']
)->name('training-courses.show');
Route::get(
    '/training-courses/{course}/enroll',
    [MarketingController::class, 'enroll']
)->name('training.enroll');

Route::get('/services/category/{category}', [MarketingController::class, 'byCategory'])
    ->name('services.byCategory');
Route::get('/services/{id}', [MarketingController::class, 'showService']);
Route::prefix('work')->group(function () {

    Route::get('/', [MarketingController::class, 'work'])
        ->name('work.index');

    Route::get('/{type}', [MarketingController::class, 'byType'])
        ->whereIn('type', ['business', 'personal', 'assets', 'training'])
        ->name('work.type');

    Route::get('/project/{project}', [MarketingController::class, 'show'])
        ->name('work.show');

});    
/*
|--------------------------------------------------------------------------
| Vue SPA (Dashboard)
|--------------------------------------------------------------------------
*/

Route::view('/dashboard', 'app');
Route::view('/{any}', 'app')->where('any', '.*');

