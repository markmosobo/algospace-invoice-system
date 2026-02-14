<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\LedgerReportService;
use App\Services\LedgerService;
use App\Models\PersonalAccount;

class LedgerController extends Controller
{
    public function profitLoss(Request $request)
    {
        $from = $request->from;
        $to   = $request->to;

        return response()->json(LedgerReportService::getProfitLoss($from, $to));
    }

    public function titheAmount(Request $request)
    {
        $from = $request->from;
        $to   = $request->to;

        return response()->json([
            'tithe_due' => LedgerReportService::getTitheAmount($from, $to)
        ]);
    }

    public function payTithe(Request $request)
    {
        $request->validate([
            'payment_account_id' => 'required|exists:personal_accounts,id'
        ]);

        $account = PersonalAccount::findOrFail($request->payment_account_id);

        $entry = LedgerService::recordTithe($account);

        return response()->json([
            'message' => $entry ? 'Tithe recorded successfully' : 'No tithe due',
            'ledger_entry' => $entry
        ]);
    }    
}
