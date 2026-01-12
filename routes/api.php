<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\InvoiceItemController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('customers', CustomerController::class);
Route::apiResource('invoices', InvoiceController::class);
Route::apiResource('invoice-items', InvoiceItemController::class);
Route::apiResource('payments', PaymentController::class);
Route::apiResource('services', ServiceController::class);
Route::apiResource('users', UserController::class);
