<?php

namespace App\Http\Controllers;

use App\Models\PersonalAccount;
use App\Services\AuditLogger;
use App\Services\LedgerReportService;
use App\Services\LedgerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LedgerController extends Controller
{
    public function profitLoss(Request $request)
    {
        $data = $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        return response()->json(
            LedgerReportService::getProfitLoss(
                $data['start_date'] ?? null,
                $data['end_date'] ?? null
            )
        );
    }

    public function titheAmount(Request $request)
    {
        $data = $request->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',
        ]);

        return response()->json([
            'tithe_due' => LedgerReportService::getTitheAmount(
                $data['from'] ?? null,
                $data['to'] ?? null
            ),
        ]);
    }

    public function payTithe(Request $request)
    {
        $data = $request->validate([
            'payment_account_id' => 'required|exists:personal_accounts,id',
        ]);

        $account = PersonalAccount::findOrFail(
            $data['payment_account_id']
        );

        $entry = DB::transaction(function () use ($account, $request) {
            $entry = LedgerService::recordTithe($account);

            if ($entry) {
                app(AuditLogger::class)->record(
                    'ledger.tithe_paid',
                    'Tithe payment recorded',
                    $entry,
                    [
                        'payment_account_id' => $account->id,
                        'ledger_entry_id' => $entry->id,
                    ],
                    $request,
                    auth('api')->id()
                );
            }

            return $entry;
        });

        return response()->json([
            'message' => $entry ? 'Tithe recorded successfully' : 'No tithe due',
            'ledger_entry' => $entry,
        ]);
    }
}