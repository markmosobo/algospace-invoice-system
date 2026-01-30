<?php

namespace App\Http\Controllers;

use App\Models\SystemLog;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

class InvoicePreviewController extends Controller
{
    /**
     * Generate invoice preview PDF (NO DB)
     */
    public function preview(Request $request)
    {
        $data = $request->validate([
            'customer' => 'nullable|array',
            'items'    => 'required|array|min:1',
            'due_date' => 'required|date',
        ]);

        $invoice = [
            'invoice_no' => 'PREVIEW-' . strtoupper(Str::random(6)),
            'customer'   => $data['customer'] ?? [],
            'items'      => $data['items'],
            'due_date'   => $data['due_date'],
            'total'      => collect($data['items'])->sum('line_total'),
            'date'       => now()->format('Y-m-d'),
            'status'     => 'Preview',
            'is_preview' => true,
        ];

        // Render Blade to HTML
        $html = view('invoices.preview', $invoice)->render();

        // Generate PDF from HTML
        $pdf = Pdf::loadHTML($html);
        $fileName = 'previews/' . $invoice['invoice_no'] . '.pdf';
        Storage::disk('public')->put($fileName, $pdf->output());

        $pdfUrl = Storage::url($fileName); // public URL for sharing

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' generated preview invoice for '.$data['customer']
        ]);        

        return response()->json([
            'pdf_url'     => $html,          // HTML for iframe preview
            'print_url'   => $html,          // reuse for printing
            'invoice_no'  => $invoice['invoice_no'],
            'pdf_file_url'=> $pdfUrl,        // new: real PDF link
        ]);
    }



    public function previewHtml(Request $request)
    {
        $customer = $request->query('customer', []);
        $items    = $request->query('items', []);
        $due_date = $request->query('due_date', now()->format('Y-m-d'));

        $total = collect($items)->sum(fn($i) => $i['line_total'] ?? 0);

        return view('invoices.preview', [
            'title'      => 'INVOICE PREVIEW',
            'status'     => 'Preview',
            'invoice_no' => 'PREVIEW-' . rand(1000,9999),
            'date'       => now()->format('Y-m-d'),
            'due_date'   => $due_date,
            'customer'   => $customer,
            'items'      => $items,
            'total'      => $total
        ]);
    }



    /**
     * Email preview invoice (NO DB)
     */
    public function email(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'pdf_path' => 'required|string',
        ]);

        Mail::raw('Attached is your invoice preview.', function ($message) use ($request) {
            $message->to($request->email)
                ->subject('Invoice Preview')
                ->attach(public_path($request->pdf_path));
        });

        //record system log
        SystemLog::create([
            'user_id' => auth('api')->user()->id,
            'description' => auth('api')->user()->name.' sent email to '.$request->email
        ]);  

        return response()->json([
            'message' => 'Preview invoice emailed successfully'
        ]);
    }


    /**
     * Print preview
     */
    public function print(Request $request)
    {
        $path = $request->query('path');

        abort_unless($path && file_exists(public_path($path)), 404);

        return response()->file(public_path($path));
    }

}
