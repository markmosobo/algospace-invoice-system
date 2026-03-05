<?php

namespace App\Http\Controllers;

use App\Models\LoyaltyCard;
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
}
