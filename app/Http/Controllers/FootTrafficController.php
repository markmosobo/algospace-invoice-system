<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\FootTraffic;
use App\Models\Invoice;
use App\Models\LedgerEntry;
use App\Models\LoyaltyCard;
use App\Models\Reward;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FootTrafficController extends Controller
{
    // Log traffic
    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'service_id'  => 'nullable|exists:services,id',
            'invoice_id'  => 'nullable|exists:invoices,id'
        ]);

        // 1️⃣ Log foot traffic
        $footTraffic = FootTraffic::create($data);

        // 2️⃣ Fetch customer with total visits
        $customer = Customer::withCount('visits')->findOrFail($request->customer_id);
        $totalVisits = $customer->visits_count;

        // 3️⃣ Fetch loyalty card (ANY status)
        $loyaltyCard = LoyaltyCard::where('customer_id', $customer->id)->first();

        $responseMessage = null;
        $rewardCreated   = false;

        // 4️⃣ Issue first loyalty card ONLY ONCE
        if (!$loyaltyCard && $totalVisits >= 5) {
            $loyaltyCard = LoyaltyCard::create([
                'customer_id' => $customer->id,
                'serial'      => 'CYB-' . str_pad($customer->id, 4, '0', STR_PAD_LEFT),
                'visits'      => 0,
                'status'      => 'active'
            ]);

            $responseMessage = "✨ First loyalty card issued!";
        }

        // 5️⃣ Increment visits ONLY if card is active
        if ($loyaltyCard && $loyaltyCard->status === 'active') {
            $previousVisits = $loyaltyCard->visits;

            $loyaltyCard->increment('visits');
            $loyaltyCard->refresh();

            // 6️⃣ Handle 10th visit reward
            if ($previousVisits < 10 && $loyaltyCard->visits >= 10) {
                $loyaltyCard->update(['status' => 'completed']);

                if ($request->invoice_id) {
                    $invoice = Invoice::find($request->invoice_id);
                    $rewardValue = $invoice ? $invoice->total_amount : 0;

                    // Create reward
                    Reward::create([
                        'customer_id' => $customer->id,
                        'reward_type' => 'gift',
                        'value'       => $rewardValue,
                        'visits'      => $loyaltyCard->visits
                    ]);

                    // Ledger entry
                    LedgerEntry::create([
                        'customer_id' => $customer->id,
                        'value'       => $rewardValue,
                        'description' => "Reward for customer #{$customer->id}"
                    ]);

                    $rewardCreated   = true;
                    $responseMessage = "🎉 Customer reached 10 visits! Reward issued.";
                }
            }
        }

        return response()->json([
            'foot_traffic'   => $footTraffic,
            'total_visits'   => $totalVisits,
            'loyalty_card'   => $loyaltyCard,
            'message'        => $responseMessage,
            'reward_created' => $rewardCreated
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

    public function storeAnon(Request $request)
    {
        FootTraffic::create([
            'customer_id' => $request->customer_id,
            'service_id' => $request->service_id,
            'invoice_id' => $request->invoice_id
        ]);
        
        return response()->json([
            'message' => 'Foot traffic logged'
        ]);
    }

    // Dashboard data: total count + breakdown
    public function dashboard()
    {
        $today = Carbon::today();

        $footTrafficList = FootTraffic::with(['customer', 'service'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Count by service
        $serviceCounts = $footTrafficList->groupBy(function($ft){
            return $ft->service ? $ft->service->name : 'General';
        })->map(fn($group) => count($group));

        return response()->json([
            'total' => $footTrafficList->count(),
            'footTrafficList' => $footTrafficList->map(function($ft){
                return [
                    'id' => $ft->id,
                    'customer_name' => $ft->customer ? $ft->customer->name : null,
                    'service_name' => $ft->service ? $ft->service->name : 'General',
                    'time_in' => $ft->created_at,
                    'invoice_id' => $ft->invoice_id
                ];
            }),
            'serviceCounts' => $serviceCounts
        ]);
    }    
}

