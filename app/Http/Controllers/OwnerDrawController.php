<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\LedgerReportService;
use App\Services\LedgerService;
use App\Models\PersonalAccount;
use App\Models\SystemLog;
use Illuminate\Support\Facades\DB;

class OwnerDrawController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'payment_account_id' => 'required|exists:personal_accounts,id',
            'from' => 'nullable|date',
            'to' => 'nullable|date',
        ]);

        $ownerDrawAmount = DB::transaction(function () use ($request) {

            /** 🔒 Lock payment account */
            $paymentAccount = PersonalAccount::lockForUpdate()
                ->findOrFail($request->payment_account_id);

            /** ✅ Get profit/loss report for filtered dates */
            $report = LedgerReportService::getProfitLoss(
                $request->from,
                $request->to
            );

            /** ❌ Enforce tithe rule */
            if (!$report['tithe_paid']) {
                throw new \Exception('Tithe must be paid before owner draw.');
            }

            /** ✅ Calculate owner draw (30% of profit after tithe) */
            $maxOwnerDraw = $report['profit_after_tithe'] * 0.3;

            if ($maxOwnerDraw <= 0) {
                throw new \Exception('No available profit for owner draw.');
            }

            /** ❌ Insufficient cash */
            if ($paymentAccount->balance < $maxOwnerDraw) {
                throw new \Exception('Insufficient account balance for owner draw.');
            }

            /** ✅ Deduct money from account */
            $paymentAccount->balance -= $maxOwnerDraw;
            $paymentAccount->save();

            /** ✅ Ledger entry */
            LedgerService::recordOwnerDraw(
                $paymentAccount,
                $maxOwnerDraw,
                'Owner draw (30% of profit after tithe)'
            );

            /** 🧾 Log */
            SystemLog::create([
                'user_id' => auth()->id(),
                'description' =>
                    auth()->user()->name .
                    ' withdrew owner draw of KES ' . $maxOwnerDraw
            ]);

            return $maxOwnerDraw;
        });

        return response()->json([
            'message' => 'Owner draw recorded successfully',
            'amount' => $ownerDrawAmount
        ]);
    }
}
