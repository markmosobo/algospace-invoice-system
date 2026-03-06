<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\LoyaltyCard;
use App\Models\SystemLog;
use Illuminate\Http\Request;

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
        $customer->save();

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
        $customer = Customer::withCount('visits')->findOrFail($id);

        // Fetch active loyalty card only
        $activeCard = LoyaltyCard::where('customer_id', $customer->id)
            ->where('status', 'active')
            ->first();

        return response()->json([
            'id' => $customer->id,
            'name' => $customer->name,
            'phone' => $customer->phone,
            'email' => $customer->email,
            'created_at' => $customer->created_at,
            'updated_at' => $customer->updated_at,

            // Visits
            'total_visits' => $customer->visits_count,           // all visits ever
            'loyalty_visits' => $activeCard ? $activeCard->visits : 0,  // only active card

            // Active card info
            'cardIssued' => $activeCard ? true : false,
            'card_serial' => $activeCard ? $activeCard->serial : null,
            'status' => $activeCard ? $activeCard->status : null,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name' => 'required|string',
        ]);

        $customer->update($request->only([
            'name','email','phone','gender'
        ]));

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
