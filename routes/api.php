<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DiaryEntryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\InvoiceItemController;
use App\Http\Controllers\ListController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PersonalAccountController;
use App\Http\Controllers\PersonalCategoryController;
use App\Http\Controllers\PersonalTransactionController;
use App\Http\Controllers\ProviderServiceController;
use App\Http\Controllers\RestockController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ServiceProviderController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SupplyController;
use App\Http\Controllers\SystemLogController;
use App\Http\Controllers\UserController;
use App\Models\ProviderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::middleware(['auth:api'])->group(function () {

    Route::apiResource('customers', CustomerController::class);
    Route::apiResource('invoices', InvoiceController::class);
    Route::apiResource('invoice-items', InvoiceItemController::class);
    Route::apiResource('payments', PaymentController::class);
    Route::apiResource('services', ServiceController::class);
    Route::apiResource('users', UserController::class);
    Route::apiResource('suppliers', SupplierController::class);
    Route::apiResource('restocks', RestockController::class);
    Route::apiResource('supplies', SupplyController::class);
    Route::apiResource('personal-categories', PersonalCategoryController::class);
    Route::apiResource('personal-accounts', PersonalAccountController::class);
    Route::apiResource('personal-transactions', PersonalTransactionController::class);
    Route::apiResource('diary-entries', DiaryEntryController::class);
    Route::apiResource('system-logs', SystemLogController::class);
    Route::apiResource('provider-services', ProviderServiceController::class);
    Route::apiResource('service-providers', ServiceProviderController::class);
    Route::apiResource('expenses', ExpenseController::class);

    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);
    Route::get('/quick-sales', [ListController::class, 'quickSales']);
    Route::post('/restock-product', [SupplyController::class, 'restock']);

    Route::put('/customers/{customer}', [CustomerController::class, 'update']);

    Route::delete('/sales/{id}', [PaymentController::class, 'destroySale']);
    Route::put('/sales/{id}', [PaymentController::class, 'updateSale']);
    Route::get('/sales/{id}', [PaymentController::class, 'showSale']);
    Route::post('/payments/{id}/complete', [PaymentController::class, 'complete']);

});
