<?php

namespace App\Http\Controllers;

use App\Services\AuditLogger;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class InvoicePreviewController extends Controller
{
    /**
     * Generate invoice preview PDF (no database invoice created).
     */
    public function preview(Request $request)
    {
        $data = $request->validate([
            'customer' => 'nullable|array',
            'items' => 'required|array|min:1',
            'items.*.line_total' => 'required|numeric|min:0',
            'due_date' => 'required|date',
        ]);

        $invoice = [
            'invoice_no' => 'PREVIEW-' . strtoupper(Str::random(6)),
            'customer' => $data['customer'] ?? [],
            'items' => $data['items'],
            'due_date' => $data['due_date'],
            'total' => collect($data['items'])->sum('line_total'),
            'date' => now()->format('Y-m-d'),
            'status' => 'Preview',
            'is_preview' => true,
        ];

        $html = view('invoices.preview', $invoice)->render();

        $pdf = Pdf::loadHTML($html);

        $fileName = 'previews/' . $invoice['invoice_no'] . '.pdf';

        Storage::disk('public')->put($fileName, $pdf->output());

        $pdfUrl = Storage::disk('public')->url($fileName);

        app(AuditLogger::class)->record(
            'invoice_preview.generated',
            'Invoice preview generated',
            null,
            [
                'invoice_no' => $invoice['invoice_no'],
                'item_count' => count($data['items']),
                'total' => $invoice['total'],
                'due_date' => $invoice['due_date'],
            ],
            $request,
            auth('api')->id()
        );

        return response()->json([
            'pdf_url' => $html,
            'print_url' => $html,
            'invoice_no' => $invoice['invoice_no'],
            'pdf_file_url' => $pdfUrl,
        ]);
    }

    /**
     * Render invoice preview HTML.
     */
    public function previewHtml(Request $request)
    {
        $data = $request->validate([
            'customer' => 'sometimes|array',
            'items' => 'sometimes|array',
            'items.*.line_total' => 'sometimes|numeric|min:0',
            'due_date' => 'sometimes|date',
        ]);

        $customer = $data['customer'] ?? [];
        $items = $data['items'] ?? [];
        $dueDate = $data['due_date'] ?? now()->format('Y-m-d');

        $total = collect($items)->sum(
            fn ($item) => $item['line_total'] ?? 0
        );

        return view('invoices.preview', [
            'title' => 'INVOICE PREVIEW',
            'status' => 'Preview',
            'invoice_no' => 'PREVIEW-' . strtoupper(Str::random(6)),
            'date' => now()->format('Y-m-d'),
            'due_date' => $dueDate,
            'customer' => $customer,
            'items' => $items,
            'total' => $total,
        ]);
    }

    /**
     * Email a generated preview PDF (no database invoice created).
     */
    public function email(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'pdf_path' => 'required|string|max:255',
        ]);

        // Accept only files inside the previews directory on public storage.
        $path = ltrim($data['pdf_path'], '/');

        if (
            !Str::startsWith($path, 'previews/') ||
            str_contains($path, '..') ||
            !Storage::disk('public')->exists($path)
        ) {
            return response()->json([
                'message' => 'Preview PDF not found',
            ], 404);
        }

        try {
            Mail::raw(
                'Attached is your invoice preview.',
                function ($message) use ($data, $path) {
                    $message->to($data['email'])
                        ->subject('Invoice Preview')
                        ->attach(
                            Storage::disk('public')->path($path),
                            [
                                'as' => basename($path),
                                'mime' => 'application/pdf',
                            ]
                        );
                }
            );

            app(AuditLogger::class)->record(
                'invoice_preview.email_sent',
                'Invoice preview emailed successfully',
                null,
                [
                    'file_name' => basename($path),
                    'channel' => 'email',
                    'status' => 'sent',
                ],
                $request,
                auth('api')->id()
            );

            return response()->json([
                'message' => 'Preview invoice emailed successfully',
            ]);
        } catch (\Throwable $e) {
            app(AuditLogger::class)->record(
                'invoice_preview.email_failed',
                'Invoice preview email failed',
                null,
                [
                    'file_name' => basename($path),
                    'channel' => 'email',
                    'status' => 'failed',
                    'error_type' => class_basename($e),
                ],
                $request,
                auth('api')->id()
            );

            return response()->json([
                'message' => 'Failed to email invoice preview',
            ], 500);
        }
    }

    /**
     * Serve a generated preview PDF for printing.
     */
    public function print(Request $request)
    {
        $data = $request->validate([
            'path' => 'required|string|max:255',
        ]);

        $path = ltrim($data['path'], '/');

        if (
            !Str::startsWith($path, 'previews/') ||
            str_contains($path, '..') ||
            !Storage::disk('public')->exists($path)
        ) {
            return response()->json([
                'message' => 'Preview PDF not found',
            ], 404);
        }

        return response()->file(
            Storage::disk('public')->path($path),
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' .
                    basename($path) . '"',
            ]
        );
    }
}