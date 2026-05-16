<?php

use App\Http\Controllers\AiChatController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BorrowController;
use App\Http\Controllers\CropController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomRewardController;
use App\Http\Controllers\CyberRequestController;
use App\Http\Controllers\CyberRequestInvoiceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DiaryEntryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\FarmAssetController;
use App\Http\Controllers\FarmController;
use App\Http\Controllers\FarmExpenseController;
use App\Http\Controllers\FarmInputController;
use App\Http\Controllers\FarmSaleController;
use App\Http\Controllers\FarmVentureController;
use App\Http\Controllers\FarmWorkerController;
use App\Http\Controllers\FarmWorkerTaskController;
use App\Http\Controllers\FirstFruitsController;
use App\Http\Controllers\FootTrafficController;
use App\Http\Controllers\HarvestController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\InvoiceItemController;
use App\Http\Controllers\InvoicePreviewController;
use App\Http\Controllers\LedgerController;
use App\Http\Controllers\LedgerReportController;
use App\Http\Controllers\ListController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\LoyaltyCardController;
use App\Http\Controllers\OnlineVisitController;
use App\Http\Controllers\OwnerDrawController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PersonalAccountController;
use App\Http\Controllers\PersonalCategoryController;
use App\Http\Controllers\PersonalTransactionController;
use App\Http\Controllers\ProviderServiceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RestockController;
use App\Http\Controllers\SeedlingController;
use App\Http\Controllers\SeedlingSaleController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ServiceProviderController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SupplyController;
use App\Http\Controllers\SystemLogController;
use App\Http\Controllers\ToDoController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CapitalInjectionController;
use App\Models\ProviderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;


// Public routes
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login2', [AuthController::class, 'login'])->name('login');

/*
|--------------------------------------------------------------------------
| EMAIL VERIFICATION (CUSTOM JWT-FRIENDLY)
|--------------------------------------------------------------------------
*/

Route::get('/email/verify/{id}', function (Request $request, $id) {

    // 🔒 validate signed URL
    if (! $request->hasValidSignature()) {
        abort(403, 'Invalid or expired verification link');
    }

    $user = User::findOrFail($id);
    // 🔥 DEBUG (temporary)
    // dd($user->email_verified_at, $user->status);

    // ✅ update verification fields
    if (is_null($user->email_verified_at)) {
        $user->email_verified_at = now();
        $user->status = 'active';
        $user->save();
    }
    
    // ✔ mark verified
    $user->update([
        'email_verified_at' => now(),
        'status' => 'active'
    ]);

    return redirect(env('FRONTEND_URL') . '/login?verified=1');

})->name('verification.verify');

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
    Route::apiResource('to-dos', ToDoController::class);
    Route::get('/todos/active', [TodoController::class, 'active']);
    Route::get('/todos-dashboard', [TodoController::class, 'dashboard']);
    Route::patch('/todos/{todo}/done', [TodoController::class, 'markDone']);
    Route::patch('/todos/{todo}/defer', [TodoController::class, 'defer']);
    Route::patch('/todos/{todo}/resume', [TodoController::class, 'resume']);
    Route::delete('/to-dos/{todo}', [TodoController::class, 'destroy']);

    Route::apiResource('farms', FarmController::class);
    Route::apiResource('farm-ventures', FarmVentureController::class);
    Route::apiResource('crops', CropController::class);
    Route::apiResource('harvests', HarvestController::class);
    Route::apiResource('seedlings', SeedlingController::class);
    Route::apiResource('seedling-sales', SeedlingSaleController::class);
    Route::apiResource('farm-expenses', FarmExpenseController::class);
    Route::apiResource('farm-inputs', FarmInputController::class);
    Route::apiResource('farm-sales', FarmSaleController::class);
    Route::apiResource('farm-workers', FarmWorkerController::class);
    Route::apiResource('worker-tasks', FarmWorkerTaskController::class);
    Route::apiResource('farm-assets', FarmAssetController::class);


    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);
    Route::get('/quick-sales', [ListController::class, 'quickSales']);
    Route::post('/restock-product', [SupplyController::class, 'restock']);

    Route::put('/customers/{customer}', [CustomerController::class, 'update']);
    Route::post('/customers/reward', [CustomRewardController::class, 'store']);

    Route::delete('/sales/{id}', [PaymentController::class, 'destroySale']);
    Route::put('/sales/{id}', [PaymentController::class, 'updateSale']);
    Route::get('/sales/{id}', [PaymentController::class, 'showSale']);
    Route::post('/payments/{id}/complete', [PaymentController::class, 'complete']);

    Route::get('/reports/profit', [ReportController::class, 'profit']);

    // PREVIEW (NO DB)
    Route::post('/invoices/preview', [InvoicePreviewController::class, 'preview']);
    Route::post('/invoices/preview/email', [InvoicePreviewController::class, 'email']);
    Route::get('/invoices/preview/print', [InvoicePreviewController::class, 'print']);

    // FINAL (DB)
    Route::get('/invoices/{id}/print', [InvoiceController::class, 'print']);
    Route::post('/invoices/{invoice}/close-unpaid', [InvoiceController::class, 'closeUnpaid']);
    
    // routes/api.php
    Route::get('/invoices/preview-html', [InvoicePreviewController::class, 'previewHtml']);

    //notifications
    Route::get('/reminders/overview', [DiaryEntryController::class, 'remindersOverview']);
    Route::put('/diary-entries/{id}/done', [DiaryEntryController::class, 'markDone']);

    // Logout user
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('ledger/profit-loss', [LedgerController::class, 'profitLoss']);
    Route::post('ledger/tithe/pay', [LedgerController::class, 'payTithe']);
    Route::get('ledger/tithe', [LedgerController::class, 'titheAmount']);
    Route::get('personal-accounts/tithe-options', [PersonalAccountController::class, 'titheOptions']);

    Route::post('/ledger/owner-draw', [OwnerDrawController::class, 'store']);

    Route::post('/ledger/loan/in', [LoanController::class, 'loanIn']);
    Route::post('/ledger/loan/out', [LoanController::class, 'repay']);

    Route::post('/ledger/transfer', [TransferController::class, 'transfer']);

    Route::get('/ledger/report', [LedgerReportController::class, 'report']);
    Route::post('/ledger/first-fruits', [FirstFruitsController::class, 'pay']);

    Route::post('/ledger/capital-injection', [CapitalInjectionController::class, 'store']);   
    Route::post('/ledger/funds-in', [CapitalInjectionController::class, 'fundsIn']);   
    Route::post('/ledger/funds-out', [LedgerReportController::class, 'fundsOut']);   
    Route::post('/ledger/adjust', [LedgerReportController::class, 'adjust']);

    Route::post('/foot-traffic', [FootTrafficController::class, 'store']);
    Route::post('/anon-foot-traffic', [FootTrafficController::class, 'storeAnon']);
    Route::get('/foot-traffic', [FootTrafficController::class, 'index']);
    Route::get('/foot-traffic-dashboard', [FootTrafficController::class, 'dashboard']);

    Route::post('/loyalty-cards', [LoyaltyCardController::class, 'store']);
    Route::get('/customers/{customer}/loyalty-card', [LoyaltyCardController::class, 'active']);
    Route::put('/loyalty-cards/{card}', [LoyaltyCardController::class, 'update']);
    Route::post('/customers/{customer}/loyalty-card/log-visit', [LoyaltyCardController::class, 'logVisit']);

    Route::post('/rewards', [CustomRewardController::class, 'store']);
    Route::post('/ledger/record-reward', [CustomRewardController::class, 'recordReward']);

    //books module
    Route::prefix('books')->group(function () {
        Route::get('/', [BookController::class, 'index']);
        Route::get('/{book}', [BookController::class, 'show']);
        Route::post('/', [BookController::class, 'store']);
        Route::put('/{book}', [BookController::class, 'update']);
        Route::delete('/{book}', [BookController::class, 'destroy']);
    });

    Route::prefix('borrow')->group(function () {
        Route::get('/', [BorrowController::class, 'index']);
        Route::post('/', [BorrowController::class, 'borrow']);
        Route::post('/return/{borrow}', [BorrowController::class, 'return']);
    });

    Route::get('/partners', [UserController::class, 'partners']);
    Route::get('/borrowers', [UserController::class, 'borrowers']);
    Route::post('/borrowers', [UserController::class, 'storeUser']);
    Route::put('/borrowers/{id}', [UserController::class, 'updateUser']);
    Route::post('/partners', [UserController::class, 'storeUser']);
    Route::put('/partners/{id}', [UserController::class, 'updateUser']);

    Route::post('/ai/chat', [AiChatController::class, 'send']);
    Route::get('/ai/chat/{session}', [AiChatController::class, 'messages']); 
        
    Route::get('/analytics/visits/total', [OnlineVisitController::class, 'totalVisits']);
    Route::get('/analytics/visits/today', [OnlineVisitController::class, 'todayVisits']);
    Route::get('/analytics/visits/unique', [OnlineVisitController::class, 'uniqueVisitors']);
    Route::get('/analytics/visits/top-pages', [OnlineVisitController::class, 'topPages']);    

    Route::get('/cyber-requests', [CyberRequestController::class, 'cyberRequests']);    
    Route::put('/cyber-requests/{id}', [CyberRequestController::class, 'updateStatus']);
    
    Route::get(
        '/cyber-requests/{id}/invoice-draft',
        [CyberRequestInvoiceController::class, 'draft']
    );

    Route::post(
        '/cyber-requests/{id}/confirm-invoice',
        [CyberRequestInvoiceController::class, 'confirm']
    );
    // routes/api.php
    Route::patch('/services/{id}/toggle', [ServiceController::class, 'toggleActive']);

});
