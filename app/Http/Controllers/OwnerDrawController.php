<?php

namespace App\Http\Controllers;

use App\Models\PersonalAccount;
use App\Services\AuditLogger;
use App\Services\LedgerReportService;
use App\Services\LedgerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OwnerDrawController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'payment_account_id' => 'required|exists:personal_accounts,id',
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',
        ]);

        $ownerDrawAmount = DB::transaction(function () use ($data, $request) {
            // Lock the payment account to prevent concurrent balance changes.
            $paymentAccount = PersonalAccount::lockForUpdate()
                ->findOrFail($data['payment_account_id']);

            // Calculate profit/loss for the requested period.
            $report = LedgerReportService::getProfitLoss(
                $data['from'] ?? null,
                $data['to'] ?? null
            );

            // Tithe must be paid before an owner draw.
            if (!$report['tithe_paid']) {
                throw ValidationException::withMessages([
                    'tithe' => ['Tithe must be paid before owner draw.'],
                ]);
            }

            // Owner draw is capped at 30% of profit after tithe.
            $maxOwnerDraw = (float) $report['profit_after_tithe'] * 0.3;

            if ($maxOwnerDraw <= 0) {
                throw ValidationException::withMessages([
                    'amount' => ['No available profit for owner draw.'],
                ]);
            }

            if ($paymentAccount->balance < $maxOwnerDraw) {
                throw ValidationException::withMessages([
                    'payment_account_id' => [
                        'Insufficient account balance for owner draw.',
                    ],
                ]);
            }

            $paymentAccount->balance -= $maxOwnerDraw;
            $paymentAccount->save();

            $entry = LedgerService::recordOwnerDraw(
                $paymentAccount,
                $maxOwnerDraw,
                'Owner draw (30% of profit after tithe)'
            );

            app(AuditLogger::class)->record(
                'owner_draw.recorded',
                'Owner draw recorded',
                $entry instanceof \Illuminate\Database\Eloquent\Model
                    ? $entry
                    : null,
                [
                    'payment_account_id' => $paymentAccount->id,
                    'amount' => $maxOwnerDraw,
                    'profit_after_tithe' => $report['profit_after_tithe'],
                    'draw_percentage' => 30,
                    'from' => $data['from'] ?? null,
                    'to' => $data['to'] ?? null,
                    'ledger_entry_id' => $entry?->id,
                ],
                $request,
                auth('api')->id()
            );

            return $maxOwnerDraw;
        });

        return response()->json([
            'message' => 'Owner draw recorded successfully',
            'amount' => $ownerDrawAmount,
        ]);
    }
}