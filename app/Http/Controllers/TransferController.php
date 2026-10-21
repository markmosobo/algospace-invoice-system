<?php

namespace App\Http\Controllers;

use App\Models\PersonalAccount;
use App\Services\AuditLogger;
use App\Services\LedgerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransferController extends Controller
{
    protected AuditLogger $auditLogger;

    public function __construct(AuditLogger $auditLogger)
    {
        $this->auditLogger = $auditLogger;
    }

    /**
     * Transfer funds between personal accounts.
     */
    public function transfer(Request $request)
    {
        $validated = $request->validate([
            'from' => 'required|different:to|exists:personal_accounts,id',
            'to' => 'required|exists:personal_accounts,id',
            'amount' => 'required|numeric|min:1|decimal:0,2',
        ]);

        DB::transaction(function () use ($validated, $request) {
            // Lock accounts in a consistent order to reduce deadlock risk.
            $accountIds = [
                (int) $validated['from'],
                (int) $validated['to'],
            ];

            sort($accountIds);

            $accounts = PersonalAccount::whereIn('id', $accountIds)
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $from = $accounts->get((int) $validated['from']);
            $to = $accounts->get((int) $validated['to']);

            if (!$from || !$to) {
                throw ValidationException::withMessages([
                    'account' => 'One or both accounts could not be found.',
                ]);
            }

            $amount = (float) $validated['amount'];

            LedgerService::transferFunds($from, $to, $amount);

            $this->auditLogger->record(
                'personal_account.transfer',
                'Funds transferred between personal accounts',
                $from,
                [
                    'from_account_id' => $from->id,
                    'to_account_id' => $to->id,
                    'amount' => $amount,
                ],
                $request,
                auth('api')->id()
            );
        }, 3);

        return response()->json([
            'message' => 'Transfer completed',
        ]);
    }
}