<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Invoice;
use App\Models\InvoiceItem;
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
            'type' => 'required|in:expense,provider_service,inventory,other',
            'invoice_id' => 'nullable|exists:invoices,id',

            'service_provider_id' => 'nullable|exists:service_providers,id',
            'provider_service_id' => 'nullable|exists:provider_services,id',

            'amount' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:255',
            'expense_date' => 'nullable|date',
        ]);

        $amount = $request->amount;
        $invoiceId = $request->invoice_id;

        // ONLY provider_service creates invoice + invoice_item
        if ($request->type === 'provider_service') {

            if (!$request->service_provider_id || !$request->provider_service_id) {
                return response()->json([
                    'message' => 'Service provider and service are required'
                ], 422);
            }

            // ALWAYS load provider service
            $providerService = ProviderService::find($request->provider_service_id);

            // EXTRA TIP: ensure provider service exists
            if (!$providerService) {
                return response()->json([
                    'message' => 'Invalid provider service'
                ], 422);
            }

            // If amount not provided, use provider service price
            if (!$amount) {
                $amount = $providerService->price;
            }

            // create invoice if not provided
            if (!$invoiceId) {
                $provider = ServiceProvider::find($request->service_provider_id);

                $invoice = Invoice::create([
                    'invoice_type' => 'expense',
                    'vendor_name' => $provider->name,
                    'invoice_number' => 'INV-EXP-' . time(),
                    'invoice_date' => now(),
                    'due_date' => now()->addDays(7),
                    'total_amount' => $amount,
                    'status' => 'pending',
                ]);

                $invoiceId = $invoice->id;
            }

            // create invoice item (ONLY for provider_service)
            InvoiceItem::create([
                'invoice_id' => $invoiceId,
                'item_type' => 'provider_service',
                'provider_service_id' => $request->provider_service_id,
                'provider_service_name' => $providerService->name,
                'expense_name' => null,
                'quantity' => 1,
                'unit_price' => $amount,
                'line_total' => $amount,
            ]);
        }

        // Create expense for all types
        $expense = Expense::create([
            'type' => $request->type,
            'invoice_id' => $invoiceId, // can be null for other types
            'service_provider_id' => $request->service_provider_id,
            'provider_service_id' => $request->provider_service_id,
            'amount' => $amount,
            'description' => $request->description,
            'expense_date' => $request->expense_date ?? now(),
        ]);

        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name . ' created expense #' . $expense->id
        ]);

        return response()->json([
            'message' => 'Expense recorded successfully',
            'expense' => $expense,
            'invoice_id' => $invoiceId
        ]);
    }

    
    public function show(string $id)
    {
        $expense = Expense::with(['serviceProvider', 'providerService', 'invoice'])
                        ->findOrFail($id);

        return response()->json([
            'expense' => $expense
        ]);
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
            'description' => auth('api')->user()->name.' updated expense #'.$expense->id
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
            'description' => auth('api')->user()->name.' deleted expense #'.$id
        ]); 

        return response()->json(['message' => 'Deleted']);
    }    
}
