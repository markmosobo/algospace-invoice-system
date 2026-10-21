<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\LoyaltyCard;
use App\Models\CustomerHistory;
use App\Models\CustomerNote;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CustomerController extends Controller
{
    /**
     * Display a listing of customers.
     */
    public function index()
    {
        $customers = Customer::with('visits', 'loyaltyCards')
            ->withCount('visits')
            ->get();

        return response()->json($customers);
    }

    /**
     * Store a newly created customer.
     */
    public function store(Request $request)
    {
        $customer = new Customer();
        $customer->name = $request->name;
        $customer->phone = $request->phone;
        $customer->email = $request->email;
        $customer->gender = $request->gender;

        if ($request->hasFile('image')) {
            $path = $request->file('image')
                ->store('customers', 'public');

            $customer->image = $path;
        }

        $customer->save();

        CustomerHistory::create([
            'customer_id' => $customer->id,
            'action' => 'Customer Created',
            'description' => 'Customer profile created',
        ]);

        app(AuditLogger::class)->record(
            'customer.created',
            "Customer profile created (ID: {$customer->id})",
            $customer,
            [
                'customer_id' => $customer->id,
                'has_image' => !empty($customer->image),
            ],
            $request,
            auth('api')->id()
        );

        return response()->json($customer);
    }

    /**
     * Display a specific customer.
     */
    public function show($id)
    {
        $customer = Customer::with([
            'notes',
            'history',
            'loyaltyCards',
            'visits',
        ])
            ->withCount('visits')
            ->findOrFail($id);

        $activeCard = LoyaltyCard::where('customer_id', $customer->id)
            ->where('status', 'active')
            ->first();

        return response()->json([
            'id' => $customer->id,
            'name' => $customer->name,
            'phone' => $customer->phone,
            'email' => $customer->email,
            'image' => $customer->image,
            'created_at' => $customer->created_at,
            'updated_at' => $customer->updated_at,

            // Visits
            'total_visits' => $customer->visits_count,
            'loyalty_visits' => $activeCard ? $activeCard->visits : 0,
            'visits' => $customer->visits,

            // Active card information
            'cardIssued' => $activeCard ? true : false,
            'card_serial' => $activeCard ? $activeCard->serial : null,
            'status' => $activeCard ? $activeCard->status : null,

            'notes' => $customer->notes,
            'history' => $customer->history,
        ]);
    }

    /**
     * Update a customer profile.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
        ]);

        $customer = Customer::findOrFail($id);

        $before = $customer->only([
            'name',
            'email',
            'phone',
            'gender',
        ]);

        $oldImage = $customer->image;
        $newImage = null;

        $customer->name = $request->name;
        $customer->email = $request->email;
        $customer->phone = $request->phone;
        $customer->gender = $request->gender;

        if ($request->hasFile('image')) {
            $newImage = $request->file('image')
                ->store('customers', 'public');

            $customer->image = $newImage;
        }

        try {
            $customer->save();
        } catch (\Throwable $e) {
            if ($newImage) {
                Storage::disk('public')->delete($newImage);
            }

            throw $e;
        }

        // Delete the previous image only after the profile is saved.
        if (
            $newImage &&
            $oldImage &&
            $oldImage !== $newImage
        ) {
            Storage::disk('public')->delete($oldImage);
        }

        $customer->refresh();

        $after = $customer->only([
            'name',
            'email',
            'phone',
            'gender',
        ]);

        $changedFields = [];

        foreach ($after as $field => $value) {
            if (($before[$field] ?? null) != $value) {
                $changedFields[] = $field;
            }
        }

        if ($newImage) {
            $changedFields[] = 'image';
        }

        if (!empty($changedFields)) {
            app(AuditLogger::class)->record(
                'customer.updated',
                "Customer profile updated (ID: {$customer->id})",
                $customer,
                [
                    'customer_id' => $customer->id,
                    'changed_fields' => $changedFields,
                    'image_replaced' => (bool) $newImage,
                ],
                $request,
                auth('api')->id()
            );

            CustomerHistory::create([
                'customer_id' => $customer->id,
                'action' => 'Profile Updated',
                'description' => 'Customer details updated',
            ]);
        }

        return response()->json([
            'message' => 'Updated',
        ]);
    }

    /**
     * Delete a customer.
     */
    public function destroy(Request $request, string $id)
    {
        $customer = Customer::findOrFail($id);

        app(AuditLogger::class)->record(
            'customer.deleted',
            "Customer deleted (ID: {$customer->id})",
            $customer,
            [
                'customer_id' => $customer->id,
                'has_image' => !empty($customer->image),
            ],
            $request,
            auth('api')->id()
        );

        $customer->delete();

        return response()->json([
            'message' => 'Deleted',
        ]);
    }

    /**
     * Add a note to a customer.
     */
    public function storeNote(Request $request, $customerId)
    {
        $request->validate([
            'note' => 'required|string|min:3',
        ]);

        $customer = Customer::findOrFail($customerId);

        $note = CustomerNote::create([
            'customer_id' => $customer->id,
            'note' => $request->note,
        ]);

        app(AuditLogger::class)->record(
            'customer.note_added',
            "Note added to customer (ID: {$customer->id})",
            $note,
            [
                'customer_id' => $customer->id,
                'note_id' => $note->id,
            ],
            $request,
            auth('api')->id()
        );

        return response()->json([
            'message' => 'Note added',
            'note' => $note,
        ]);
    }
}