<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\LoyaltyCard;
use App\Models\SystemLog;
use App\Models\CustomerHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customers = Customer::with('visits','loyaltyCards')->withCount('visits')->get();        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' retrieved customers'
        ]); 
                 
        return response()->json($customers);      
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $customer = new Customer();
        $customer->name = $request->name;
        $customer->phone = $request->phone;
        $customer->email = $request->email;
        $customer->gender = $request->gender; 

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('customers', 'public');
            $customer->image = $path;
        }

        $customer->save();

        CustomerHistory::create([
            'customer_id' => $customer->id,
            'action' => 'Customer Created',
            'description' => 'Customer profile created'
        ]);

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' created customer #'.$customer->id
        ]);          
                
        return response()->json($customer);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Fetch customer with total visits count
        $customer = Customer::with([
            'notes',
            'history',
            'loyaltyCards',
            'visits'
        ])
        ->withCount('visits')
        ->findOrFail($id);
        
        // Fetch active loyalty card only
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
            'total_visits' => $customer->visits_count,           // all visits ever
            'loyalty_visits' => $activeCard ? $activeCard->visits : 0,  // only active card

            // Active card info
            'cardIssued' => $activeCard ? true : false,
            'card_serial' => $activeCard ? $activeCard->serial : null,
            'status' => $activeCard ? $activeCard->status : null,

            'notes' => $customer->notes,

            'history' => $customer->history
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
        ]);

        $customer = Customer::findOrFail($id);

        $customer->name = $request->name;
        $customer->email = $request->email;
        $customer->phone = $request->phone;
        $customer->gender = $request->gender; 

        if ($request->hasFile('image')) {

            // OPTIONAL: delete old image
            if ($customer->image && Storage::disk('public')->exists($customer->image)) {
                Storage::disk('public')->delete($customer->image);
            }

            $path = $request->file('image')->store('customers', 'public');
            $customer->image = $path;
        }

        $customer->save();

        CustomerHistory::create([
            'customer_id' => $customer->id,
            'action' => 'Profile Updated',
            'description' => 'Customer details updated'
        ]);

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' updated details for customer #'.$customer->id
        ]);         

        return response()->json(['message' => 'Updated']);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Customer::destroy($id);

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' deleted customer #'.$id
        ]);         
        return response()->json(['message' => 'Deleted']);
    }
}
