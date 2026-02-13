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
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function stats()
    {
        $user = Auth::user();
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
        ->orWhere('name','')
        ->orWhere('name','I&M ALGOSPACE LIMITED')
        ->sum('balance');
        
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
                        'officeTotal'     => PersonalAccount::where('name','POCHI MPESA')
                                          ->orWhere('name','STAWISHA SACCO - SHOP')
                                          ->orWhere('name','I&M BANK')
                                          ->orWhere('name','CASH')
                                          ->sum('balance'),  
                        'officeCash'     => PersonalAccount::whereIn('name', ['CASH', 'POCHI MPESA'])->sum('balance'),  
                        'officeBank'     => PersonalAccount::where('name','STAWISHA SACCO - SHOP')
                                          ->orWhere('name','I&M BANK')
                                          ->sum('balance'),
                        'officeReceivables'  => DB::table('invoices')
                                            ->whereColumn('amount_paid', '<', 'total_amount')
                                            ->select(DB::raw('SUM(total_amount - amount_paid) as total_receivables'))
                                            ->value('total_receivables')  
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
                        'grandTotal'                  => PersonalAccount::sum('balance'),
                        'accountTotal'                => $accountTotal,
                        'liquidTotal'                 => $liquidTotal,
                        'semiLiquidTotal'             => $semiLiquidTotal,
                        'savingsTotal'                => $savingsTotal  
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
