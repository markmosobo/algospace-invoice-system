<?php

namespace App\Http\Controllers;

use App\Models\FootTraffic;
use App\Models\LoyaltyCard;
use Illuminate\Http\Request;

class FootTrafficController extends Controller
{
    // Log traffic
    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'service_id' => 'nullable|exists:services,id',
            'invoice_id' => 'nullable|exists:invoices,id'
        ]);

        $footTraffic = FootTraffic::create($data);

        // Check if customer has an active loyalty card
        $card = LoyaltyCard::where('customer_id', $request->customer_id)
                        ->where('status', 'active')
                        ->first();

        if ($card) {
            $card->addVisit(); // Increment the loyalty card visits
        }

        return response()->json([
            'foot_traffic' => $footTraffic,
            'loyalty_card' => $card // null if card doesn't exist yet
        ], 201);
    }

    // List traffic (optional for dashboard)
    public function index()
    {
        $traffic = FootTraffic::with(['customer', 'service', 'invoice'])
                    ->orderBy('arrival_time', 'desc')
                    ->get();

        return response()->json($traffic);
    }
}

