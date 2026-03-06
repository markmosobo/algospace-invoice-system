<?php

namespace App\Http\Controllers;

use App\Models\LedgerEntry;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Reward;

class CustomRewardController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'reward_type' => 'required|string',
            'value' => 'nullable|numeric',
            'visits' => 'required|integer',
        ]);

        $reward = Reward::create([
            'customer_id' => $request->customer_id,
            'type' => $request->reward_type,
            'value' => $request->value ?? 0,
            'visits_at_reward' => $request->visits,
        ]);

        return response()->json([
            'success' => true,
            'reward' => $reward,
            'message' => 'Reward logged successfully 🎁'
        ]);
    }

    public function recordReward(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'reward_type' => 'required|string',
            'value'       => 'nullable|numeric',
            'visits'      => 'required|integer',
        ]);

        // 1️⃣ Create the reward
        $reward = Reward::create([
            'customer_id' => $request->customer_id,
            'type'        => $request->reward_type,
            'value'       => $request->value ?? 0,
            'visits_at_reward' => $request->visits,
        ]);

        // 2️⃣ Record reward in ledger
        if (($request->value ?? 0) > 0) {
            LedgerEntry::create([
                'entry_date'       => now(),
                'debit_account_id' => PersonalAccount::where('name', 'Loyalty Rewards Expense')->firstOrFail()->id,
                'credit_account_id'=> PersonalAccount::where('name', 'Loyalty Rewards Liability')->firstOrFail()->id,
                'amount'           => $request->value,
                'entry_type'       => 'expense',
                'reference'        => 'Reward for customer #' . $request->customer_id,
                'description'      => 'Reward issued for loyalty card completion',
                'created_by'       => auth()->id(),
            ]);
        }

        return response()->json([
            'success' => true,
            'reward'  => $reward,
            'message' => 'Reward logged successfully 🎁 and ledger entry created'
        ]);
    }
}