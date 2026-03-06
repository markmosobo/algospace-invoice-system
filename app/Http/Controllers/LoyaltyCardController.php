<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\LoyaltyCard;
use App\Models\Reward;
use Illuminate\Http\Request;

class LoyaltyCardController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'serial' => 'required|unique:loyalty_cards,serial'
        ]);

        $card = LoyaltyCard::create([
            'customer_id' => $request->customer_id,
            'serial' => $request->serial,
            'visits' => 0,
        ]);

        return response()->json($card);
    }  
    
    // Get active loyalty card
    public function active(Customer $customer)
    {
        $card = $customer->loyaltyCards()->where('status', 'active')->first();
        return response()->json($card);
    }

    // Log a visit and handle reward
    public function logVisit(Request $request, Customer $customer)
    {
        $card = $customer->activeCard();

        if (!$card) {
            return response()->json(['message' => 'No active card found'], 404);
        }

        // Increment visits
        $card->visits += 1;

        if ($card->visits >= 10) {
            // Complete card
            $card->status = 'completed';
            $card->save();

            // Issue reward
            Reward::create([
                'customer_id' => $customer->id,
                'reward_type' => 'gift',
                'value' => 0,
                'visits' => 10,
                'redeemed' => false,
            ]);

            // Reset customer's card status so a new card can be issued
            $customer->update(['cardIssued' => null]);

            // Optionally: Create a new card automatically
            $newCard = LoyaltyCard::create([
                'customer_id' => $customer->id,
                'serial' => strtoupper(uniqid('CARD-')),
                'visits' => 0,
                'status' => 'active'
            ]);

            return response()->json([
                'message' => 'Reward issued and new card created',
                'completed_card' => $card,
                'new_card' => $newCard
            ]);
        } else {
            $card->save();

            return response()->json([
                'message' => 'Visit logged',
                'card' => $card
            ]);
        }
    }

    // Update visits and status
    public function update(Request $request, $id)
    {
        $card = LoyaltyCard::findOrFail($id);

        $card->update([
            'visits' => $request->visits,
            'status' => $request->status,
        ]);

        return response()->json($card);
    }
}
