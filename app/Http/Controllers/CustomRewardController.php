<?php

namespace App\Http\Controllers;

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
}