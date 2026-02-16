<?php

namespace App\Http\Controllers;

use App\Models\PersonalAccount;
use App\Services\LedgerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CapitalInjectionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'account_id' => 'required|exists:personal_accounts,id',
            'amount' => 'required|numeric|min:1',
        ]);

        DB::transaction(function () use ($request) {

            $account = PersonalAccount::lockForUpdate()
                ->findOrFail($request->account_id);

            $account->increment('balance', $request->amount);

            LedgerService::recordCapitalInjection(
                $account,
                $request->amount,
                'Owner capital injection'
            );
        });

        return response()->json(['message' => 'Capital injected']);
    }    
}
