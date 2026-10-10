<?php

namespace App\Http\Controllers;

use App\Models\LedgerEntry;
use App\Models\PersonalAccount;
use App\Models\Reward;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomRewardController extends Controller
{
    /**
     * Record a customer reward.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'reward_type' => 'required|string',
            'value' => 'nullable|numeric|min:0',
            'visits' => 'required|integer|min:0',
        ]);

        $reward = Reward::create([
            'customer_id' => $data['customer_id'],
            'type' => $data['reward_type'],
            'value' => $data['value'] ?? 0,
            'visits_at_reward' => $data['visits'],
        ]);

        app(AuditLogger::class)->record(
            'loyalty_reward.created',
            "Loyalty reward recorded (ID: {$reward->id})",
            $reward,
            [
                'reward_id' => $reward->id,
                'customer_id' => $reward->customer_id,
                'reward_type' => $reward->type,
                'value' => $reward->value,
                'visits_at_reward' => $reward->visits_at_reward,
                'ledger_entry_created' => false,
            ],
            $request,
            auth('api')->id()
        );

        return response()->json([
            'success' => true,
            'reward' => $reward,
            'message' => 'Reward logged successfully 🎁',
        ]);
    }

    /**
     * Record a reward and its corresponding ledger entry.
     */
    public function recordReward(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'reward_type' => 'required|string',
            'value' => 'nullable|numeric|min:0',
            'visits' => 'required|integer|min:0',
        ]);

        $result = DB::transaction(function () use ($data) {
            // 1. Create the reward.
            $reward = Reward::create([
                'customer_id' => $data['customer_id'],
                'type' => $data['reward_type'],
                'value' => $data['value'] ?? 0,
                'visits_at_reward' => $data['visits'],
            ]);

            $ledgerEntry = null;

            // 2. Create the ledger entry for monetary rewards.
            if (($data['value'] ?? 0) > 0) {
                $expenseAccount = PersonalAccount::where(
                    'name',
                    'Loyalty Rewards Expense'
                )->firstOrFail();

                $liabilityAccount = PersonalAccount::where(
                    'name',
                    'Loyalty Rewards Liability'
                )->firstOrFail();

                $ledgerEntry = LedgerEntry::create([
                    'entry_date' => now(),
                    'debit_account_id' => $expenseAccount->id,
                    'credit_account_id' => $liabilityAccount->id,
                    'amount' => $data['value'],
                    'entry_type' => 'expense',
                    'reference' => 'Reward for customer #' . $data['customer_id'],
                    'description' => 'Reward issued for loyalty card completion',
                    'created_by' => auth('api')->id(),
                ]);
            }

            return [
                'reward' => $reward,
                'ledger_entry' => $ledgerEntry,
            ];
        });

        $reward = $result['reward'];
        $ledgerEntry = $result['ledger_entry'];

        // 3. Audit the reward after the transaction succeeds.
        app(AuditLogger::class)->record(
            'loyalty_reward.created',
            "Loyalty reward recorded (ID: {$reward->id})",
            $reward,
            [
                'reward_id' => $reward->id,
                'customer_id' => $reward->customer_id,
                'reward_type' => $reward->type,
                'value' => $reward->value,
                'visits_at_reward' => $reward->visits_at_reward,
                'ledger_entry_created' => $ledgerEntry !== null,
                'ledger_entry_id' => $ledgerEntry?->id,
            ],
            $request,
            auth('api')->id()
        );

        // 4. Separately audit the accounting entry when one exists.
        if ($ledgerEntry) {
            app(AuditLogger::class)->record(
                'loyalty_reward.ledger_recorded',
                "Ledger entry created for loyalty reward #{$reward->id}",
                $ledgerEntry,
                [
                    'reward_id' => $reward->id,
                    'customer_id' => $reward->customer_id,
                    'ledger_entry_id' => $ledgerEntry->id,
                    'amount' => $ledgerEntry->amount,
                    'debit_account_id' => $ledgerEntry->debit_account_id,
                    'credit_account_id' => $ledgerEntry->credit_account_id,
                ],
                $request,
                auth('api')->id()
            );
        }

        return response()->json([
            'success' => true,
            'reward' => $reward,
            'message' => 'Reward logged successfully 🎁 and ledger entry created',
        ]);
    }
}