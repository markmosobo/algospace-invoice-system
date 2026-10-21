<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\FootTraffic;
use App\Models\Invoice;
use App\Models\LedgerEntry;
use App\Models\LoyaltyCard;
use App\Models\Reward;
use App\Services\AuditLogger;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FootTrafficController extends Controller
{
    /**
     * Log customer traffic and process loyalty rewards.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'service_id' => 'nullable|exists:services,id',
            'invoice_id' => 'nullable|exists:invoices,id',
        ]);

        $result = DB::transaction(function () use ($request, $data) {
            // Lock the customer to reduce concurrent loyalty-processing conflicts.
            $customer = Customer::whereKey($data['customer_id'])
                ->lockForUpdate()
                ->firstOrFail();

            $footTraffic = FootTraffic::create($data);

            app(AuditLogger::class)->record(
                'foot_traffic.logged',
                "Customer foot traffic logged (ID: {$footTraffic->id})",
                $footTraffic,
                [
                    'foot_traffic_id' => $footTraffic->id,
                    'customer_id' => $customer->id,
                    'service_id' => $footTraffic->service_id,
                    'invoice_id' => $footTraffic->invoice_id,
                ],
                $request,
                auth('api')->id()
            );

            $customer->loadCount('visits');
            $totalVisits = $customer->visits_count;

            // Retrieve a loyalty card regardless of its status.
            $loyaltyCard = LoyaltyCard::where('customer_id', $customer->id)
                ->lockForUpdate()
                ->first();

            $responseMessage = null;
            $rewardCreated = false;

            // Issue the first loyalty card after five visits.
            if (!$loyaltyCard && $totalVisits >= 5) {
                $loyaltyCard = LoyaltyCard::create([
                    'customer_id' => $customer->id,
                    'serial' => 'CYB-' . str_pad(
                        $customer->id,
                        4,
                        '0',
                        STR_PAD_LEFT
                    ),
                    'visits' => 0,
                    'status' => 'active',
                ]);

                app(AuditLogger::class)->record(
                    'loyalty_card.created',
                    "Loyalty card issued to customer #{$customer->id}",
                    $loyaltyCard,
                    [
                        'loyalty_card_id' => $loyaltyCard->id,
                        'customer_id' => $customer->id,
                        'serial' => $loyaltyCard->serial,
                    ],
                    $request,
                    auth('api')->id()
                );

                $responseMessage = 'First loyalty card issued!';
            }

            // Increment visits only while the card is active.
            if ($loyaltyCard && $loyaltyCard->status === 'active') {
                $previousVisits = $loyaltyCard->visits;

                $loyaltyCard->increment('visits');
                $loyaltyCard->refresh();

                // Handle the 10th visit.
                if (
                    $previousVisits < 10 &&
                    $loyaltyCard->visits >= 10
                ) {
                    $loyaltyCard->update([
                        'status' => 'completed',
                    ]);

                    app(AuditLogger::class)->record(
                        'loyalty_card.completed',
                        "Loyalty card completed for customer #{$customer->id}",
                        $loyaltyCard,
                        [
                            'loyalty_card_id' => $loyaltyCard->id,
                            'customer_id' => $customer->id,
                            'visits' => $loyaltyCard->visits,
                        ],
                        $request,
                        auth('api')->id()
                    );

                    if (!empty($data['invoice_id'])) {
                        $invoice = Invoice::findOrFail($data['invoice_id']);
                        $rewardValue = $invoice->total_amount;

                        $reward = Reward::create([
                            'customer_id' => $customer->id,
                            'reward_type' => 'gift',
                            'value' => $rewardValue,
                            'visits' => $loyaltyCard->visits,
                        ]);

                        app(AuditLogger::class)->record(
                            'loyalty_reward.issued',
                            "Loyalty reward issued to customer #{$customer->id}",
                            $reward,
                            [
                                'reward_id' => $reward->id,
                                'customer_id' => $customer->id,
                                'value' => $rewardValue,
                                'visits' => $loyaltyCard->visits,
                                'invoice_id' => $invoice->id,
                            ],
                            $request,
                            auth('api')->id()
                        );

                        LedgerEntry::create([
                            'customer_id' => $customer->id,
                            'value' => $rewardValue,
                            'description' => "Reward for customer #{$customer->id}",
                        ]);

                        app(AuditLogger::class)->record(
                            'loyalty_reward.ledger_recorded',
                            "Ledger entry recorded for loyalty reward #{$reward->id}",
                            $reward,
                            [
                                'reward_id' => $reward->id,
                                'customer_id' => $customer->id,
                                'value' => $rewardValue,
                            ],
                            $request,
                            auth('api')->id()
                        );

                        $rewardCreated = true;
                        $responseMessage =
                            'Customer reached 10 visits! Reward issued.';
                    }
                }
            }

            return [
                'foot_traffic' => $footTraffic,
                'total_visits' => $totalVisits,
                'loyalty_card' => $loyaltyCard,
                'message' => $responseMessage,
                'reward_created' => $rewardCreated,
            ];
        });

        return response()->json($result, 201);
    }

    /**
     * List foot traffic.
     */
    public function index()
    {
        $traffic = FootTraffic::with([
            'customer',
            'service',
            'invoice',
        ])
            ->orderBy('arrival_time', 'desc')
            ->get();

        return response()->json($traffic);
    }

    /**
     * Log anonymous or basic foot traffic.
     */
    public function storeAnon(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'service_id' => 'nullable|exists:services,id',
            'invoice_id' => 'nullable|exists:invoices,id',
        ]);

        $footTraffic = FootTraffic::create($data);

        app(AuditLogger::class)->record(
            'foot_traffic.anonymous_logged',
            "Basic foot traffic logged (ID: {$footTraffic->id})",
            $footTraffic,
            [
                'foot_traffic_id' => $footTraffic->id,
                'customer_id' => $footTraffic->customer_id,
                'service_id' => $footTraffic->service_id,
                'invoice_id' => $footTraffic->invoice_id,
            ],
            $request,
            auth('api')->id()
        );

        return response()->json([
            'message' => 'Foot traffic logged',
        ]);
    }

    /**
     * Dashboard traffic totals and service breakdown.
     */
    public function dashboard()
    {
        $footTrafficList = FootTraffic::with([
            'customer',
            'service',
        ])
            ->orderBy('created_at', 'desc')
            ->get();

        $serviceCounts = $footTrafficList
            ->groupBy(function ($ft) {
                return $ft->service
                    ? $ft->service->name
                    : 'General';
            })
            ->map(fn ($group) => $group->count());

        return response()->json([
            'total' => $footTrafficList->count(),

            'footTrafficList' => $footTrafficList->map(function ($ft) {
                return [
                    'id' => $ft->id,
                    'customer_name' => $ft->customer
                        ? $ft->customer->name
                        : null,
                    'service_name' => $ft->service
                        ? $ft->service->name
                        : 'General',
                    'time_in' => $ft->created_at,
                    'invoice_id' => $ft->invoice_id,
                ];
            }),

            'serviceCounts' => $serviceCounts,
        ]);
    }
}