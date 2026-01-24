<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Invoice;
use App\Models\ServiceProvider;
use App\Models\ProviderService;
use App\Models\SystemLog;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::with('serviceProvider', 'providerService', 'invoice')->get();
        $serviceProviders = ServiceProvider::get();
        $providerServices = ProviderService::get();
        $invoices =Invoice::with('customer')->where('status', 'pending')->get();

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' retrieved expenses'
        ]);

        // Return as JSON
        return response()->json([
            'expenses' => $expenses,
            'serviceProviders' => $serviceProviders,
            'providerServices' => $providerServices,
            'invoices' => $invoices,
        ]);
    } 
    
    public function store(Request $request)
    {
        $request->validate([
            'invoice_id'   => 'sometimes|exists:invoices,id',
            'service_provider_id'   => 'required|exists:service_providers,id',
            'provider_service_id'   => 'required|exists:provider_services,id',
            'amount'       => 'required|numeric|min:1',
            // 'expense_date' => 'required|date',
            'description' => 'nullable|string|max:20',
        ]);

        $invoice = Invoice::find($request->invoice_id);

        // create expense
        $expense = Expense::create([
            'invoice_id'   => $request->invoice_id,
            'service_provider_id'   => $request->service_provider_id,
            'provider_service_id'   => $request->provider_service_id,
            'amount'       => $request->amount,
            'expense_date' => $request->expense_date,
            'description'      => $request->description ?? null,
        ]);

        // ✅ Update invoice status if paid
        // if ($invoice->total_amount <= $request->amount) {
        //     $invoice->update(['status' => 'paid']);
        // }

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' created expense id '.$expense->id
        ]);

        return response()->json([
            'message' => 'Expense recorded successfully',
            'expense' => $expense
        ]);
    } 
    
    public function show(string $id)
    {
        $expense = Expense::find($id);
        return response()->json($expense);
    }   
    
    public function update(Request $request, string $id)
    {
        $expense = Expense::findOrFail($id);

        $request->validate([
            'invoice_id'   => 'sometimes|exists:invoices,id',
            'service_provider_id'   => 'required|exists:service_providers,id',
            'provider_service_id'   => 'required|exists:provider_services,id',
            'amount'       => 'required|numeric|min:1',
            // 'expense_date' => 'required|date',
            'description' => 'nullable|string|max:20',
        ]);

        $expense->update([
            'invoice_id'   => $request->invoice_id,
            'service_provider_id'   => $request->service_provider_id,
            'provider_service_id'   => $request->provider_service_id,
            'amount'       => $request->amount,
            'expense_date' => $request->expense_date,
            'description'      => $request->description ?? null,
        ]);

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' updated expense id '.$expense->id
        ]);         

        return response()->json([
            'message' => 'Expense updated successfully',
            'expense' => $expense
        ]);
    }
    
    public function destroy(string $id)
    {
        Expense::destroy($id);

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' deleted expense id '.$id
        ]); 

        return response()->json(['message' => 'Deleted']);
    }    
}
