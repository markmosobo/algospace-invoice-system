<?php

namespace App\Http\Controllers;

use App\Models\PersonalAccount;
use App\Services\LedgerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransferController extends Controller
{
public function transfer(Request $request)
{
    $request->validate([
        'from' => 'required|different:to|exists:personal_accounts,id',
        'to' => 'required|exists:personal_accounts,id',
        'amount' => 'required|numeric|min:1',
    ]);

    $from = PersonalAccount::lockForUpdate()->findOrFail($request->from);
    $to = PersonalAccount::lockForUpdate()->findOrFail($request->to);

    LedgerService::transferFunds($from, $to, $request->amount);

    return response()->json(['message' => 'Transfer completed']);
}
    
}
