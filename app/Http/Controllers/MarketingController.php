<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Customer;
use App\Models\Service;
use Carbon\Carbon;

class MarketingController extends Controller
{
    public function index()
    {
        $documentsProcessed = Invoice::count(); 
        // or InvoiceItem::count()
        $customersCount = Customer::count();
        $servicesCount = Service::count();

        $firstActivity = collect([
            Invoice::min('created_at'),
            Customer::min('created_at'),
        ])->filter()->sort()->first();

        $monthsServing = $firstActivity
            ? Carbon::parse($firstActivity)->diffInMonths(now())
            : 0;

        return view('marketing', compact(
            'documentsProcessed',
            'customersCount',
            'servicesCount',
            'monthsServing'
        ));
    }    
}
