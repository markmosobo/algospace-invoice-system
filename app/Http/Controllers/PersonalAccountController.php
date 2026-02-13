<?php

namespace App\Http\Controllers;

use App\Models\SystemLog;
use Illuminate\Http\Request;
use App\Models\PersonalAccount;

class PersonalAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $personalAccounts = PersonalAccount::all();
        $accountTotal = PersonalAccount::sum('balance');
        $liquidTotal = PersonalAccount::where('name','POCHI MPESA')
        ->orWhere('name','CASH')
        ->orWhere('name','PERSONAL MPESA')
        ->orWhere('name','I&M BANK')
        ->sum('balance');
        $semiLiquidTotal = PersonalAccount::where('name','POSTBANK')
        ->orWhere('name','EQUITY BANK ACCOUNT')
        ->orWhere('name','JAR SAVINGS')
        ->orWhere('name','MUM/MARK JAR')
        ->sum('balance');
        $savingsTotal = PersonalAccount::where('name','CARITAS JIKAZE NRB SAVINGS')
        ->orWhere('name','STAWISHA SACCO - FARM')
        ->orWhere('name','STAWISHA SACCO - SHOP')
        ->orWhere('name','I&M ALGOSPACE LIMITED')
        ->sum('balance');

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' retrieved personal accounts'
        ]);

        return response()->json([
            'message' => 'Whatsapp receipt count updated successfully',
            'personalAccounts' => $personalAccounts,
            'accountTotal' => $accountTotal,
            'liquidTotal' => $liquidTotal,
            'semiLiquidTotal' => $semiLiquidTotal,
            'savingsTotal' => $savingsTotal
        ]);

        // return response()->json($personalAccounts);        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'name' => 'required|string|max:255',
            'balance' => 'required|numeric|min:0',
            'currency' => 'required|string|max:50',
        ]);

        // Create the personal account
        $personalAccount = PersonalAccount::create([
            'name' => $request->name,
            'sub_type' => $request->sub_type,
            'balance' => $request->balance,
            'currency' => $request->currency,
        ]);

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' created personal account #'.$personalAccount->id
        ]);        

        return response()->json([
            'message' => 'Personal account created successfully',
            'personalAccount' => $personalAccount
        ], 201);        
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $personalAccount = PersonalAccount::find($id);

        if (!$personalAccount) {
            return response()->json([
                'message' => 'Personal account not found'
            ], 404);
        }

        return response()->json($personalAccount);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $personalAccount = PersonalAccount::find($id);

        if (!$personalAccount) {
            return response()->json([
                'message' => 'Personal account not found'
            ], 404);
        }

        // Validate request
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'balance' => 'sometimes|required|numeric|min:0',
            'currency' => 'sometimes|required|string|max:50',
        ]);

        // Update fields if provided
        if ($request->has('name')) {
            $personalAccount->name = $request->name;
        }
        if ($request->has('sub_type')) {
            $personalAccount->sub_type = $request->sub_type;
        }
        if ($request->has('balance')) {
            $personalAccount->balance = $request->balance;
        }
        if ($request->has('currency')) {
            $personalAccount->currency = $request->currency;
        }

        $personalAccount->save();

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' updated personal account #'.$personalAccount->id
        ]);        

        return response()->json([
            'message' => 'Personal account updated successfully',
            'personalAccount' => $personalAccount
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $personalAccount = PersonalAccount::find($id);

        if (!$personalAccount) {
            return response()->json([
                'message' => 'Personal account not found'
            ], 404);
        }

        $personalAccount->delete();

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' deleted personal account #'.$id
        ]);        

        return response()->json([
            'message' => 'Personal account deleted successfully'
        ]);
    }
}
