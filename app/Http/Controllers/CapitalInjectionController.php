<?php

namespace App\Http\Controllers;

use App\Models\PersonalAccount;
use App\Services\AuditLogger;
use App\Services\LedgerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CapitalInjectionController extends Controller
{
    /**
     * Record an owner capital injection.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'account_id' => 'required|exists:personal_accounts,id',
            'amount' => 'required|numeric|min:1',
        ]);

        DB::transaction(function () use ($data, $request) {
            $account = PersonalAccount::lockForUpdate()
                ->findOrFail($data['account_id']);

            $amount = $data['amount'];
            $balanceBefore = $account->balance;

            $account->increment('balance', $amount);
            $account->refresh();

            LedgerService::recordCapitalInjection(
                $account,
                $amount,
                'Owner capital injection'
            );

            // Audit the completed capital injection.
            app(AuditLogger::class)->record(
                'capital.injection_recorded',
                'Owner capital injected into personal account',
                $account,
                [
                    'account_id' => $account->id,
                    'amount' => $amount,
                    'balance_before' => $balanceBefore,
                    'balance_after' => $account->balance,
                    'currency' => 'KES',
                ],
                $request
            );
        });

        return response()->json([
            'message' => 'Capital injected',
        ]);
    }

    /**
     * Record owner funds returned to savings.
     */
    public function fundsIn(Request $request)
    {
        $data = $request->validate([
            'account_id' => 'required|exists:personal_accounts,id',
            'amount' => 'required|numeric|min:1',
        ]);

        DB::transaction(function () use ($data, $request) {
            $account = PersonalAccount::lockForUpdate()
                ->findOrFail($data['account_id']);

            $amount = $data['amount'];
            $balanceBefore = $account->balance;

            // Preserve existing behavior: LedgerService handles
            // the redeposit operation; do not increment separately.
            LedgerService::recordOwnerRedeposit(
                $account,
                $amount,
                'Owner funds returned to savings'
            );

            $account->refresh();

            // Audit the completed redeposit.
            app(AuditLogger::class)->record(
                'capital.owner_redeposit_recorded',
                'Owner funds redeposited into savings',
                $account,
                [
                    'account_id' => $account->id,
                    'amount' => $amount,
                    'balance_before' => $balanceBefore,
                    'balance_after' => $account->balance,
                    'currency' => 'KES',
                ],
                $request
            );
        });

        return response()->json([
            'message' => 'Owner funds redeposited',
        ]);
    }
}