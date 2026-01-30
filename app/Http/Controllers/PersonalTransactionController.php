<?php

namespace App\Http\Controllers;

use App\Models\PersonalAccount;
use App\Models\PersonalCategory;
use App\Models\PersonalTransaction;
use App\Models\SystemLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PersonalTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $accounts = PersonalAccount::all();
        $categories = PersonalCategory::all();
        $personalTransactions = PersonalTransaction::with('account', 'category')->get();

        // Record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' retrieved personal transactions'
        ]);

        return response()->json([
            'personalTransactions' => $personalTransactions,
            'accounts'     => $accounts,
            'categories'   => $categories
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'account_id' => 'required|integer|min:1', // no foreign key constraint if desired
            'category_id' => 'nullable|integer|min:1',
            'type'   => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'transaction_date' => 'nullable|date',
        ]);

        // Set transaction_date to current date if not provided
        $transactionDate = $request->transaction_date ?? Carbon::now();

        // Create new transaction 
        $personalTransaction = PersonalTransaction::create([
            'account_id' => $request->account_id,
            'category_id' => $request->category_id,
            'type'   => $request->type,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'description' => $request->description,
            'transaction_date' => $transactionDate,
        ]);

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' created personal transaction #'.$personalTransaction->id
        ]);        

        return response()->json([
            'message' => 'Personal transaction created successfully',
            'transaction' => $personalTransaction
        ], 201);        
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $transaction = PersonalTransaction::find($id);

        if (!$transaction) {
            return response()->json([
                'message' => 'Transaction not found'
            ], 404);
        }

        return response()->json($transaction);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $transaction = PersonalTransaction::find($id);

        if (!$transaction) {
            return response()->json([
                'message' => 'Transaction not found'
            ], 404);
        }

        // Validate input
        $request->validate([
            'account_id' => 'sometimes|integer|min:1',
            'category_id' => 'sometimes|integer|min:1',
            'type'   => 'sometimes|in:income,expense',
            'amount' => 'sometimes|numeric|min:0',
            'payment_method' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'transaction_date' => 'nullable|date',
        ]);

        // Update fields if provided
        $transaction->account_id = $request->account_id ?? $transaction->account_id;
        $transaction->category_id = $request->category_id ?? $transaction->category_id;
        $transaction->type = $request->type ?? $transaction->type;
        $transaction->amount = $request->amount ?? $transaction->amount;
        $transaction->payment_method = $request->payment_method ?? $transaction->payment_method;
        $transaction->description = $request->description ?? $transaction->description;
        $transaction->transaction_date = $request->transaction_date ?? $transaction->transaction_date;

        $transaction->save();

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' updated personal transaction #'.$transaction->id
        ]);         

        return response()->json([
            'message' => 'Transaction updated successfully',
            'transaction' => $transaction
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $transaction = PersonalTransaction::find($id);

        if (!$transaction) {
            return response()->json([
                'message' => 'Transaction not found'
            ], 404);
        }

        $transaction->delete();

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' deleted personal transaction #'.$id
        ]);         

        return response()->json([
            'message' => 'Transaction deleted successfully'
        ]);
    }
}
