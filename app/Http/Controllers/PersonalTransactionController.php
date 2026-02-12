<?php

namespace App\Http\Controllers;

use App\Models\PersonalAccount;
use App\Models\PersonalCategory;
use App\Models\PersonalTransaction;
use App\Models\SystemLog;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
        $request->validate([
            'account_id' => 'required|exists:personal_accounts,id',
            'category_id' => 'nullable|exists:personal_categories,id',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'transaction_date' => 'nullable|date',
        ]);

        DB::transaction(function () use ($request, &$personalTransaction) {

            $transactionDate = $request->transaction_date ?? Carbon::now();

            // Lock the account row to prevent race conditions
            $account = PersonalAccount::lockForUpdate()->findOrFail($request->account_id);

            // Create transaction
            $personalTransaction = PersonalTransaction::create([
                'account_id' => $account->id,
                'category_id' => $request->category_id,
                'type' => $request->type,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'description' => $request->description,
                'transaction_date' => $transactionDate,
            ]);

            // Update account balance
            if ($request->type === 'income') {
                $account->balance += $request->amount;
            } else {
                $account->balance -= $request->amount;
            }

            $account->save();

            // Log
            SystemLog::create([
                'user_id' => auth('api')->user()->id,
                'description' =>
                    auth('api')->user()->name .
                    ' created personal transaction #' .
                    $personalTransaction->id
            ]);
        });

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
