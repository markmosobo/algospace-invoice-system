<?php

namespace App\Http\Controllers;

use App\Models\PersonalAccount;
use App\Services\LedgerReportService;
use App\Services\LedgerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FirstFruitsController extends Controller
{
public function pay(Request $request)
{
    $request->validate([
        'account_id' => 'required|exists:personal_accounts,id',
        'amount'     => 'required|numeric|min:1',
    ]);

    DB::transaction(function () use ($request) {
        $account = PersonalAccount::lockForUpdate()->findOrFail($request->account_id);

        if ($account->balance < $request->amount) {
            throw new \Exception('Insufficient funds.');
        }

        LedgerService::recordFirstFruits(
            $account,
            $request->amount,        // <-- passes the user input
            'First Fruits payment'
        );
    });

    return response()->json([
        'message' => 'First Fruits paid: KES ' . $request->amount
    ]);
}

   
}
