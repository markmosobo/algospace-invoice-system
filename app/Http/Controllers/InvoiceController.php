<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\SystemLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\InvoiceService;
use App\Models\InvoiceSend;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvoiceMail;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{

    protected $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pendinginvoices = Invoice::with('customer')
            ->where('status', 'pending')
            ->get()
            ->map(function($invoice) {
                $invoice->is_overdue = $invoice->due_date < now();
                return $invoice;
            });
        $invoices = Invoice::with('customer')->where('status', 'paid')->get();
        $customers = Customer::get();

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' retrieved invoices'
        ]);

        // Return as JSON
        return response()->json([
            'pendinginvoices' => $pendinginvoices,
            'invoices' => $invoices,
            'customers' => $customers,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_id'    => 'required|exists:customers,id',
            'due_date'       => 'nullable|date',
            'status'         => 'nullable|in:pending,paid,overdue',
            'total_amount'   => 'required|numeric|min:0',
            'items'          => 'required|array'
        ]);

        $customer = Customer::findOrFail($request->customer_id);

        $data = [
            'items' => $request->items,
            'due_date' => $request->due_date,
            'status' => $request->status,
            'total_amount' => $request->total_amount,
        ];

        $invoice = $this->invoiceService->create(
            $data,
            $customer,
            'walkin',
            null
        );

        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' created invoice via walk-in #'.$invoice->id
        ]);

        return response()->json([
            'message' => 'Invoice created successfully',
            'invoice' => $invoice
        ]);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $invoice = Invoice::with(['customer', 'items'])
                        ->findOrFail($id);

        return response()->json([
            'invoice' => $invoice
        ]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'invoice_type' => 'required|in:sales,expense',
            'customer_id'  => 'nullable|exists:customers,id',
            'vendor_name'  => 'nullable|string|max:255',
            'invoice_date' => 'required|date',
            'due_date'     => 'nullable|date',
            'total_amount' => 'required|numeric|min:0',
            'status'       => 'nullable|in:pending,paid,overdue',
        ]);

        $invoice = Invoice::findOrFail($id);

        $invoice->update([
            'invoice_type' => $request->invoice_type,
            'customer_id'  => $request->invoice_type === 'sales' ? $request->customer_id : null,
            'vendor_name'  => $request->invoice_type === 'expense' ? $request->vendor_name : null,
            'invoice_date' => $request->invoice_date,
            'due_date'     => $request->due_date,
            'total_amount' => $request->total_amount,
            'status'       => $request->status,
        ]);

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' updated invoice #'.$invoice->id
        ]);        

        return response()->json([
            'message' => 'Invoice updated successfully',
            'invoice' => $invoice
        ]);
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Invoice::destroy($id);

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' deleted invoice #'.$id
        ]);

        return response()->json(['message' => 'Deleted']);
    }

    public function download($id)
    {
        $invoice = Invoice::findOrFail($id);

                //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' downloaded invoice #'.$id
        ]);

        return response()->file(storage_path('app/public/' . $invoice->pdf_path));
    }

    /**
     * Print FINAL invoice
     */
    public function print($id)
    {
        $invoice = Invoice::findOrFail($id);

                //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' printed invoice #'.$id
        ]);

        return response()->file(storage_path('app/public/' . $invoice->pdf_path));
    }

    public function closeUnpaid(Request $request, Invoice $invoice)
    {
        // Prevent closing paid invoices
        if ($invoice->status === 'paid') {
            return response()->json([
                'message' => 'Paid invoices cannot be closed as unpaid.'
            ], 422);
        }

        $request->validate([
            'note' => 'nullable|string|max:1000'
        ]);

        // Start a transaction to keep things consistent
        DB::transaction(function () use ($invoice, $request) {

            // 1️⃣ Update invoice
            $invoice->update([
                'status' => 'closed_unpaid',
                'closed_note' => $request->note,
                'closed_at' => now(),
            ]);

            // 2️⃣ Update customer if invoice is linked to a customer
            if ($invoice->customer_id) {
                $customer = $invoice->customer;

                $customer->increment('unpaid_invoices_count'); // +1 unpaid
                $customer->last_unpaid_invoice_at = now();
                $customer->is_risky = true; // mark as risky

                $customer->save();
            }

        });

        return response()->json([
            'message' => 'Invoice closed as unpaid and customer flagged as risky.',
            'invoice' => $invoice
        ]);
    }  
    
public function sendPdf(Request $request)
{
    $invoice = Invoice::with(['customer', 'items'])
        ->findOrFail($request->invoice_id);

    try {

        // =========================
        // PDF GENERATION
        // =========================
        $pdf = Pdf::loadView('invoices.invoice', [
            'title'          => 'INVOICE',
            'invoice_number' => $invoice->invoice_number, // ✅ FIXED
            'date'           => $invoice->created_at->format('Y-m-d'),
            'due_date'       => optional($invoice->due_date)->format('Y-m-d'),
            'status'         => $invoice->status,

            'customer' => [
                'name'  => $invoice->customer->name ?? 'Walk-in Customer',
                'email' => $invoice->customer->email ?? '',
                'phone' => $invoice->customer->phone ?? '',
            ],

            'items' => $invoice->items->map(function ($item) {
                return [
                    'name'       => $item->description,
                    'price'      => $item->unit_price,
                    'quantity'   => $item->quantity,
                    'line_total' => $item->line_total,
                ];
            })->toArray(),

            'total' => $invoice->total_amount,
        ]);

        $pdfContent = $pdf->output();

        // =========================
        // EMAIL SEND
        // =========================
        Mail::to($invoice->customer->email)
            ->send(new InvoiceMail($invoice, $pdfContent));

        // =========================
        // TRACKING
        // =========================
        InvoiceSend::create([
            'invoice_id' => $invoice->id,
            'channel'    => 'email',
            'status'     => 'sent',
            'sent_at'    => now()
        ]);

        return response()->json([
            'message' => 'Invoice sent successfully'
        ]);

    } catch (\Exception $e) {

        \Log::error('Invoice send failed', [
            'invoice_id' => $invoice->id,
            'error' => $e->getMessage()
        ]);

        InvoiceSend::create([
            'invoice_id' => $invoice->id,
            'channel'    => 'email',
            'status'     => 'failed',
        ]);

        return response()->json([
            'message' => 'Failed to send invoice',
            'error'   => $e->getMessage()
        ], 500);
    }
}
    
    public function tracking($id)
    {
        $invoice = Invoice::with(['sends'])->findOrFail($id);

        return response()->json([
            'invoice_id' => $invoice->id,
            'invoice_number' => $invoice->invoice_number,

            // SUMMARY
            'summary' => [
                'total_sends' => $invoice->sends->count(),
                'email_sent' => $invoice->sends->where('channel', 'email')->count(),
                'whatsapp_sent' => $invoice->sends->where('channel', 'whatsapp')->count(),
                'failed' => $invoice->sends->where('status', 'failed')->count(),
            ],

            // FULL LOGS
            'logs' => $invoice->sends->map(function ($send) {
                return [
                    'id' => $send->id,
                    'channel' => $send->channel,
                    'status' => $send->status,
                    'sent_at' => $send->sent_at,
                    'created_at' => $send->created_at,
                ];
            }),
        ]);
    }    
}
