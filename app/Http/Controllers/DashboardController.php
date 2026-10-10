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
use App\Services\AuditLogger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function stats()
    {
        $user = Auth::user();

        // Audit the dashboard access without recording financial figures.
        if ($user) {
            app(AuditLogger::class)->record(
                'dashboard.stats_viewed',
                "{$user->name} retrieved dashboard statistics",
                null,
                [
                    'role' => $user->role,
                ],
                request(),
                $user->id
            );
        }

        switch ($user?->role) {

            /* =========================
               OFFICE DASHBOARD
            ========================= */
            case 'office':

                return response()->json([
                    'stats' => [
                        'services' => Service::count(),
                        'suppliers' => Supplier::count(),
                        'supplies' => Supply::count(),
                        'payments' => Payment::count(),
                        'customers' => Customer::count(),
                        'invoices' => Invoice::count(),

                        'officeTotal' => PersonalAccount::whereIn('name', [
                            'I&M ALGOSPACE CYBER PAYBILL',
                            'CASH',
                        ])->sum('balance'),

                        'officeCash' => PersonalAccount::whereIn('name', [
                            'CASH',
                        ])->sum('balance'),

                        'officeBank' => PersonalAccount::whereIn('name', [
                            'I&M BANK',
                        ])->sum('balance'),

                        'officeReceivables' => DB::table('invoices')
                            ->whereColumn('amount_paid', '<', 'total_amount')
                            ->select(DB::raw(
                                'SUM(total_amount - amount_paid) as total_receivables'
                            ))
                            ->value('total_receivables'),
                    ],
                ]);

            /* =========================
               PERSONAL DASHBOARD
            ========================= */
            case 'personal':

                return response()->json([
                    'stats' => [
                        'personalAccounts' => PersonalAccount::count(),
                        'personalCategories' => PersonalCategory::count(),
                        'personalTransactions' => PersonalTransaction::count(),

                        'grandTotal' => PersonalAccount::sum('balance'),
                        'accountTotal' => PersonalAccount::sum('balance'),

                        'liquidTotal' => PersonalAccount::whereIn('name', [
                            'POCHI MPESA',
                            'CASH',
                            'PERSONAL MPESA',
                            'I&M BANK',
                        ])->sum('balance'),

                        'semiLiquidTotal' => PersonalAccount::whereIn('name', [
                            'EQUITY BANK ACCOUNT',
                            'POSTBANK',
                            'I&M ALGOSPACE LIMITED',
                        ])->sum('balance'),

                        'savingsTotal' => PersonalAccount::whereIn('name', [
                            'CARITAS JIKAZE NRB SAVINGS',
                            'JAR SAVINGS',
                            'MUM/MARK JAR',
                            'POSTBANK',
                        ])->sum('balance'),
                    ],
                ]);

            /* =========================
               DEFAULT
            ========================= */
            default:
                return response()->json([
                    'stats' => [],
                ]);
        }
    }
}