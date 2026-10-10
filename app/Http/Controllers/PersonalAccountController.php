<?php

namespace App\Http\Controllers;

use App\Models\PersonalAccount;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PersonalAccountController extends Controller
{
    /**
     * Display a listing of personal accounts and totals.
     */
    public function index()
    {
        $personalAccounts = PersonalAccount::all();

        $accountTotal = PersonalAccount::sum('balance');

        $liquidTotal = PersonalAccount::whereIn('name', [
            'POCHI MPESA',
            'CASH',
            'PERSONAL MPESA',
            'I&M BANK',
        ])->sum('balance');

        $semiLiquidTotal = PersonalAccount::whereIn('name', [
            'POSTBANK',
            'EQUITY BANK ACCOUNT',
            'JAR SAVINGS',
            'MUM/MARK JAR',
        ])->sum('balance');

        $savingsTotal = PersonalAccount::whereIn('name', [
            'CARITAS JIKAZE NRB SAVINGS',
            'STAWISHA SACCO - FARM',
            'STAWISHA SACCO - SHOP',
            'I&M ALGOSPACE LIMITED',
        ])->sum('balance');

        return response()->json([
            'message' => 'Whatsapp receipt count updated successfully',
            'personalAccounts' => $personalAccounts,
            'accountTotal' => $accountTotal,
            'liquidTotal' => $liquidTotal,
            'semiLiquidTotal' => $semiLiquidTotal,
            'savingsTotal' => $savingsTotal,
        ]);
    }

    /**
     * Store a newly created personal account.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'sub_type' => 'nullable|string|max:255',
            'balance' => 'required|numeric|min:0',
            'currency' => 'required|string|max:50',
        ]);

        $personalAccount = DB::transaction(function () use ($data, $request) {
            $account = PersonalAccount::create($data);

            app(AuditLogger::class)->record(
                'personal_account.created',
                'Personal account created',
                $account,
                [
                    'account_id' => $account->id,
                    'name' => $account->name,
                    'sub_type' => $account->sub_type,
                    'initial_balance' => $account->balance,
                    'currency' => $account->currency,
                ],
                $request,
                auth('api')->id()
            );

            return $account;
        });

        return response()->json([
            'message' => 'Personal account created successfully',
            'personalAccount' => $personalAccount,
        ], 201);
    }

    /**
     * Display the specified personal account.
     */
    public function show(string $id)
    {
        $personalAccount = PersonalAccount::find($id);

        if (!$personalAccount) {
            return response()->json([
                'message' => 'Personal account not found',
            ], 404);
        }

        return response()->json($personalAccount);
    }

    /**
     * Update the specified personal account.
     */
    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'sub_type' => 'sometimes|nullable|string|max:255',
            'balance' => 'sometimes|required|numeric|min:0',
            'currency' => 'sometimes|required|string|max:50',
        ]);

        $personalAccount = DB::transaction(function () use (
            $data,
            $id,
            $request
        ) {
            $account = PersonalAccount::whereKey($id)
                ->lockForUpdate()
                ->first();

            if (!$account) {
                return null;
            }

            $before = [
                'name' => $account->name,
                'sub_type' => $account->sub_type,
                'balance' => $account->balance,
                'currency' => $account->currency,
            ];

            $account->fill($data);
            $changes = $account->getDirty();

            if (!empty($changes)) {
                $account->save();

                $after = [
                    'name' => $account->name,
                    'sub_type' => $account->sub_type,
                    'balance' => $account->balance,
                    'currency' => $account->currency,
                ];

                app(AuditLogger::class)->record(
                    'personal_account.updated',
                    'Personal account updated',
                    $account,
                    [
                        'account_id' => $account->id,
                        'before' => $before,
                        'after' => $after,
                        'changed_fields' => array_keys($changes),
                    ],
                    $request,
                    auth('api')->id()
                );
            }

            return $account;
        });

        if (!$personalAccount) {
            return response()->json([
                'message' => 'Personal account not found',
            ], 404);
        }

        return response()->json([
            'message' => 'Personal account updated successfully',
            'personalAccount' => $personalAccount,
        ]);
    }

    /**
     * Remove the specified personal account.
     */
    public function destroy(string $id)
    {
        $deleted = DB::transaction(function () use ($id) {
            $account = PersonalAccount::whereKey($id)
                ->lockForUpdate()
                ->first();

            if (!$account) {
                return false;
            }

            app(AuditLogger::class)->record(
                'personal_account.deleted',
                'Personal account deleted',
                $account,
                [
                    'account_id' => $account->id,
                    'name' => $account->name,
                    'balance_at_deletion' => $account->balance,
                    'currency' => $account->currency,
                ],
                request(),
                auth('api')->id()
            );

            $account->delete();

            return true;
        });

        if (!$deleted) {
            return response()->json([
                'message' => 'Personal account not found',
            ], 404);
        }

        return response()->json([
            'message' => 'Personal account deleted successfully',
        ]);
    }

    /**
     * Get eligible accounts for tithe payments.
     */
    public function titheOptions()
    {
        $accounts = PersonalAccount::where('category', 'shop_working_capital')
            ->whereIn('account_type', ['cash', 'mpesa', 'bank'])
            ->where('balance', '>', 0)
            ->get([
                'id',
                'name',
                'balance',
                'account_type',
                'category',
            ]);

        return response()->json([
            'personalAccounts' => $accounts,
        ]);
    }
}