<?php

namespace App\Http\Controllers;

use App\Models\FootTraffic;
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

        return response()->json($footTraffic, 201);
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

