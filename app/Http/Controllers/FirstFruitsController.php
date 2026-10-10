<?php

namespace App\Http\Controllers;

use App\Models\PersonalAccount;
use App\Services\AuditLogger;
use App\Services\LedgerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FirstFruitsController extends Controller
{
    /**
     * Record a First Fruits payment.
     */
    public function pay(Request $request)
    {
        $validated = $request->validate([
            'account_id' => 'required|exists:personal_accounts,id',
            'amount' => 'required|numeric|min:1',
        ]);

        $amount = (float) $validated['amount'];

        DB::transaction(function () use ($request, $validated, $amount) {
            $account = PersonalAccount::lockForUpdate()
                ->findOrFail($validated['account_id']);

            if ($account->balance < $amount) {
                throw ValidationException::withMessages([
                    'amount' => ['Insufficient funds.'],
                ]);
            }

            LedgerService::recordFirstFruits(
                $account,
                $amount,
                'First Fruits payment'
            );

            app(AuditLogger::class)->record(
                'first_fruits.paid',
                'First Fruits payment recorded',
                $account,
                [
                    'account_id' => $account->id,
                    'amount' => $amount,
                ],
                $request,
                auth('api')->id()
            );
        });

        return response()->json([
            'message' => 'First Fruits paid: KES ' . $validated['amount'],
        ]);
    }
}