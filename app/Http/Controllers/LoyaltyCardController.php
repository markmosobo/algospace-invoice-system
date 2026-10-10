<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\LoyaltyCard;
use App\Models\Reward;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LoyaltyCardController extends Controller
{
    /**
     * Create a loyalty card.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'serial' => 'required|string|max:100|unique:loyalty_cards,serial',
        ]);

        $card = LoyaltyCard::create([
            'customer_id' => $data['customer_id'],
            'serial' => $data['serial'],
            'visits' => 0,
        ]);

        app(AuditLogger::class)->record(
            'loyalty_card.created',
            'Loyalty card created',
            $card,
            [
                'customer_id' => $card->customer_id,
                'serial' => $card->serial,
            ],
            $request,
            auth('api')->id()
        );

        return response()->json($card);
    }

    /**
     * Get the customer's active loyalty card.
     */
    public function active(Customer $customer)
    {
        $card = $customer->loyaltyCards()
            ->where('status', 'active')
            ->first();

        return response()->json($card);
    }

    /**
     * Log a visit and issue a reward when the card is completed.
     */
    public function logVisit(Request $request, Customer $customer)
    {
        $result = DB::transaction(function () use ($request, $customer) {
            // Lock the active card to prevent concurrent visit updates.
            $card = $customer->loyaltyCards()
                ->where('status', 'active')
                ->lockForUpdate()
                ->first();

            if (!$card) {
                return null;
            }

            $card->visits += 1;

            if ($card->visits >= 10) {
                $card->visits = 10;
                $card->status = 'completed';
                $card->save();

                $reward = Reward::create([
                    'customer_id' => $customer->id,
                    'reward_type' => 'gift',
                    'value' => 0,
                    'visits' => 10,
                    'redeemed' => false,
                ]);

                $customer->update([
                    'cardIssued' => null,
                ]);

                $newCard = LoyaltyCard::create([
                    'customer_id' => $customer->id,
                    'serial' => strtoupper(
                        'CARD-' . Str::random(12)
                    ),
                    'visits' => 0,
                    'status' => 'active',
                ]);

                $audit = app(AuditLogger::class);

                $audit->record(
                    'loyalty_card.completed',
                    'Loyalty card completed after ten visits',
                    $card,
                    [
                        'customer_id' => $customer->id,
                        'visits' => $card->visits,
                        'new_card_id' => $newCard->id,
                    ],
                    $request,
                    auth('api')->id()
                );

                $audit->record(
                    'loyalty_reward.issued',
                    'Loyalty reward issued',
                    $reward,
                    [
                        'customer_id' => $customer->id,
                        'reward_id' => $reward->id,
                        'reward_type' => $reward->reward_type,
                        'visits' => $reward->visits,
                    ],
                    $request,
                    auth('api')->id()
                );

                $audit->record(
                    'loyalty_card.created',
                    'Replacement loyalty card created',
                    $newCard,
                    [
                        'customer_id' => $customer->id,
                        'replaces_card_id' => $card->id,
                        'serial' => $newCard->serial,
                    ],
                    $request,
                    auth('api')->id()
                );

                return [
                    'completed' => true,
                    'card' => $card,
                    'new_card' => $newCard,
                ];
            }

            $card->save();

            $card->increment('visits', 0); // No additional increment; retain saved visit count.

            app(AuditLogger::class)->record(
                'loyalty_card.visit_logged',
                'Customer loyalty visit logged',
                $card,
                [
                    'customer_id' => $customer->id,
                    'visits' => $card->visits,
                ],
                $request,
                auth('api')->id()
            );

            return [
                'completed' => false,
                'card' => $card,
            ];
        });

        if (!$result) {
            return response()->json([
                'message' => 'No active card found',
            ], 404);
        }

        if ($result['completed']) {
            return response()->json([
                'message' => 'Reward issued and new card created',
                'completed_card' => $result['card'],
                'new_card' => $result['new_card'],
            ]);
        }

        return response()->json([
            'message' => 'Visit logged',
            'card' => $result['card'],
        ]);
    }

    /**
     * Manually update a loyalty card.
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'visits' => 'required|integer|min:0|max:10',
            'status' => 'required|in:active,completed',
        ]);

        $card = LoyaltyCard::findOrFail($id);

        $before = [
            'visits' => $card->visits,
            'status' => $card->status,
        ];

        $card->update($data);

        if ($card->wasChanged(['visits', 'status'])) {
            app(AuditLogger::class)->record(
                'loyalty_card.updated',
                'Loyalty card manually updated',
                $card,
                [
                    'before' => $before,
                    'after' => [
                        'visits' => $card->visits,
                        'status' => $card->status,
                    ],
                ],
                $request,
                auth('api')->id()
            );
        }

        return response()->json($card);
    }
}