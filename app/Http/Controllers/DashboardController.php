<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PersonalAccount;
use App\Models\PersonalCategory;
use App\Models\PersonalTransaction;
use App\Models\Service;
use App\Models\Supplier;
use App\Models\Supply;
use App\Models\SystemLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function stats()
    {
        $user = Auth::user();

        // ✅ Log once
        SystemLog::create([
            'user_id' => $user->id,
            'description' => $user->name . ' retrieved dashboard stats'
        ]);
        
        switch ($user->role) {

            /* =========================
               OFFICE DASHBOARD
            ========================= */
            case 'office':

                return response()->json([
                    'stats' => [
                        'services'        => Service::count(),
                        'suppliers'        => Supplier::count(),
                        'supplies'        => Supply::count(),
                        'payments'        => Payment::count(),
                        'customers'        => Customer::count(),
                        'invoices'        => Invoice::count(),
                    ]
                ]);

            /* =========================
               PERSONAL DASHBOARD
            ========================= */
            case 'personal':

                return response()->json([
                    'stats' => [
                        'personalAccounts'        => PersonalAccount::count(),
                        'personalCategories'        => PersonalCategory::count(),
                        'personalTransactions'        => PersonalTransaction::count(),
                    ]
                ]);

            /* =========================
               DEFAULT
            ========================= */
            default:
                return response()->json(['stats' => []]);
        }
    }    
}
