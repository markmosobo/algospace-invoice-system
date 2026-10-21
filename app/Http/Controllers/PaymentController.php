<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PersonalAccount;
use App\Models\PersonalTransaction;
use App\Services\AuditLogger;
use App\Services\LedgerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * List payments and pending invoices.
     */
    public function index()
    {
        $payments = Payment::with('invoice')->get();

        $invoices = Invoice::with('customer')
            ->where('status', 'pending')
            ->get();

        return response()->json([
            'payments' => $payments,
            'invoices' => $invoices,
        ]);
    }

    /**
     * Record a payment.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'method' => 'required|in:cash,mpesa,bank,card,other',
            'mpesa_code' => 'nullable|required_if:method,mpesa|string|max:20',
            'comment' => 'nullable|string|max:255',
        ]);

        $payment = DB::transaction(function () use ($data, $request) {
            $actorId = auth('api')->id();

            // Lock the invoice before recording payment.
            $invoice = Invoice::lockForUpdate()
                ->findOrFail($data['invoice_id']);

            // Resolve the receiving account.
            $account = null;

            if ($data['method'] === 'cash') {
                $account = PersonalAccount::where('name', 'CASH')
                    ->lockForUpdate()
                    ->firstOrFail();
            } elseif ($data['method'] === 'mpesa') {
                $account = PersonalAccount::where('name', 'POCHI MPESA')
                    ->lockForUpdate()
                    ->firstOrFail();
            } elseif ($data['method'] === 'bank') {
                $account = PersonalAccount::where(
                    'name',
                    'I&M ALGOSPACE CYBER PAYBILL'
                )->lockForUpdate()->firstOrFail();
            }

            $amount = (float) $data['amount'];

            $payment = Payment::create([
                'invoice_id' => $invoice->id,
                'amount' => $amount,
                'payment_date' => $data['payment_date'],
                'method' => $data['method'],
                'mpesa_code' => $data['method'] === 'mpesa'
                    ? ($data['mpesa_code'] ?? null)
                    : null,
                'comment' => $data['comment'] ?? null,
            ]);

            // Credit the receiving account where applicable.
            if ($account) {
                $account->balance += $amount;
                $account->save();

                PersonalTransaction::create([
                    'account_id' => $account->id,
                    'type' => 'income',
                    'amount' => $amount,
                    'reference' => 'Invoice #' . $invoice->id,
                    'source' => 'payment',
                    'created_at' => now(),
                ]);

                $revenueAccount = PersonalAccount::where(
                    'name',
                    'SALES REVENUE'
                )->first();

                if (!$revenueAccount) {
                    throw new \RuntimeException(
                        'SALES REVENUE account is not configured'
                    );
                }

                if ($amount > 0) {
                    LedgerService::recordSale(
                        $account,
                        $revenueAccount,
                        $amount,
                        'Invoice #' . $invoice->id
                    );
                }
            }

            // Update invoice payment status.
            $invoice->amount_paid += $amount;

            if ($invoice->amount_paid >= $invoice->total_amount) {
                $invoice->status = 'paid';
            }

            $invoice->save();

            // Automatically create course enrolments where applicable.
            if ($amount > 0) {
                $this->createCourseEnrollments($invoice);
            }

            app(AuditLogger::class)->record(
                'payment.recorded',
                'Payment recorded',
                $payment,
                [
                    'payment_id' => $payment->id,
                    'invoice_id' => $invoice->id,
                    'amount' => $amount,
                    'method' => $payment->method,
                    'payment_date' => $payment->payment_date,
                    'account_id' => $account?->id,
                    'invoice_status' => $invoice->status,
                ],
                $request,
                $actorId
            );

            return $payment;
        });

        return response()->json([
            'message' => 'Payment recorded and account credited successfully',
            'payment' => $payment,
        ], 201);
    }

    /**
     * Display a payment.
     */
    public function show(string $id)
    {
        $payment = Payment::find($id);

        return response()->json($payment);
    }

    /**
     * Update a payment.
     */
    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'nullable|date',
            'method' => 'required|in:cash,mpesa,bank',
            'mpesa_code' => 'sometimes|required_if:method,mpesa|string|max:20',
            'comment' => 'nullable|string|max:255',
        ]);

        $payment = DB::transaction(function () use ($data, $id, $request) {
            $payment = Payment::lockForUpdate()->findOrFail($id);

            $before = [
                'invoice_id' => $payment->invoice_id,
                'amount' => $payment->amount,
                'payment_date' => $payment->payment_date,
                'method' => $payment->method,
            ];

            $payment->update([
                'invoice_id' => $data['invoice_id'],
                'amount' => $data['amount'],
                'payment_date' => $data['payment_date']
                    ?? $payment->payment_date,
                'method' => $data['method'],
                'mpesa_code' => $data['mpesa_code']
                    ?? $payment->mpesa_code,
                'comment' => $data['comment'] ?? null,
            ]);

            app(AuditLogger::class)->record(
                'payment.updated',
                'Payment updated',
                $payment,
                [
                    'before' => $before,
                    'after' => [
                        'invoice_id' => $payment->invoice_id,
                        'amount' => $payment->amount,
                        'payment_date' => $payment->payment_date,
                        'method' => $payment->method,
                    ],
                ],
                $request,
                auth('api')->id()
            );

            return $payment;
        });

        return response()->json([
            'message' => 'Payment updated successfully',
            'payment' => $payment,
        ]);
    }

    /**
     * Delete a payment.
     */
    public function destroy(string $id)
    {
        DB::transaction(function () use ($id, &$payment) {
            $payment = Payment::lockForUpdate()->findOrFail($id);

            app(AuditLogger::class)->record(
                'payment.deleted',
                'Payment deleted',
                $payment,
                [
                    'payment_id' => $payment->id,
                    'invoice_id' => $payment->invoice_id,
                    'amount' => $payment->amount,
                    'method' => $payment->method,
                ],
                request(),
                auth('api')->id()
            );

            $payment->delete();
        });

        return response()->json([
            'message' => 'Deleted',
        ]);
    }

    /**
     * Delete a sale and its invoice.
     */
    public function destroySale($id)
    {
        DB::transaction(function () use ($id) {
            $payment = Payment::lockForUpdate()->findOrFail($id);
            $invoice = $payment->invoice;

            app(AuditLogger::class)->record(
                'payment.sale_deleted',
                'Sale payment and associated invoice deleted',
                $payment,
                [
                    'payment_id' => $payment->id,
                    'invoice_id' => $invoice?->id,
                    'amount' => $payment->amount,
                    'method' => $payment->method,
                ],
                request(),
                auth('api')->id()
            );

            $payment->delete();

            if ($invoice) {
                $invoice->delete();
            }
        });

        return response()->json([
            'message' => 'Sale deleted successfully',
        ]);
    }

    /**
     * Complete an invoice by recording the remaining balance.
     */
    public function complete($id)
    {
        $result = DB::transaction(function () use ($id) {
            $payment = Payment::findOrFail($id);

            $invoice = Invoice::lockForUpdate()
                ->findOrFail($payment->invoice_id);

            $totalPaid = $invoice->payments()->sum('amount');
            $balance = $invoice->total_amount - $totalPaid;

            $completionPayment = null;

            if ($balance > 0) {
                $completionPayment = Payment::create([
                    'invoice_id' => $invoice->id,
                    'amount' => $balance,
                    'payment_date' => now(),
                    'method' => $payment->method,
                    'mpesa_code' => $payment->mpesa_code,
                    'comment' => 'Balance cleared',
                ]);

                $totalPaid += $balance;
            }

            $invoice->update([
                'amount_paid' => $totalPaid,
                'status' => 'paid',
            ]);

            app(AuditLogger::class)->record(
                'payment.invoice_completed',
                'Invoice marked fully paid',
                $invoice,
                [
                    'invoice_id' => $invoice->id,
                    'trigger_payment_id' => $payment->id,
                    'completion_payment_id' => $completionPayment?->id,
                    'balance_cleared' => max(0, $balance),
                    'amount_paid' => $totalPaid,
                    'total_amount' => $invoice->total_amount,
                ],
                request(),
                auth('api')->id()
            );

            return [
                'invoice' => $invoice,
                'amount_paid' => $totalPaid,
            ];
        });

        return response()->json([
            'message' => 'Invoice fully paid',
            'amount_paid' => $result['invoice']->amount_paid,
            'total_amount' => $result['invoice']->total_amount,
            'invoice_status' => 'paid',
        ]);
    }

    /**
     * Display sale details.
     */
    public function showSale($id)
    {
        $payment = Payment::with([
            'invoice.items',
            'invoice.customer',
        ])->findOrFail($id);

        $invoice = $payment->invoice;

        return response()->json([
            'invoice_no' => $invoice->invoice_number,
            'customer_name' => $invoice->customer->name,
            'items' => $invoice->items,
            'invoice_total' => $invoice->total_amount,
            'total_paid' => $invoice->payments()->sum('amount'),
            'status' => $invoice->status,
            'method' => $payment->method,
            'payment_date' => $payment->payment_date,
            'mpesa_code' => $payment->mpesa_code,
            'comment' => $payment->comment,
        ]);
    }

    /**
     * Update a sale payment.
     */
    public function updateSale(Request $request, $id)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:1',
            'method' => 'required|in:cash,mpesa,bank',
            'payment_date' => 'required|date',
            'mpesa_code' => 'nullable|string|max:20',
        ]);

        $result = DB::transaction(function () use ($data, $id, $request) {
            $payment = Payment::lockForUpdate()->findOrFail($id);
            $invoice = Invoice::lockForUpdate()
                ->findOrFail($payment->invoice_id);

            $before = [
                'amount' => $payment->amount,
                'method' => $payment->method,
                'payment_date' => $payment->payment_date,
            ];

            $payment->update($data);

            $totalPaid = $invoice->payments()->sum('amount');

            $invoice->update([
                'amount_paid' => $totalPaid,
                'status' => $totalPaid >= $invoice->total_amount
                    ? 'paid'
                    : 'pending',
            ]);

            app(AuditLogger::class)->record(
                'payment.sale_updated',
                'Sale payment updated',
                $payment,
                [
                    'invoice_id' => $invoice->id,
                    'before' => $before,
                    'after' => [
                        'amount' => $payment->amount,
                        'method' => $payment->method,
                        'payment_date' => $payment->payment_date,
                    ],
                    'invoice_status' => $invoice->status,
                    'total_paid' => $totalPaid,
                ],
                $request,
                auth('api')->id()
            );

            return $invoice;
        });

        return response()->json([
            'message' => 'Payment updated successfully',
            'invoice_status' => $result->status,
        ]);
    }

    /**
     * Create course enrolments for courses on the invoice.
     */
    private function createCourseEnrollments($invoice)
    {
        $invoice->load([
            'customer',
            'items.service.sessions',
        ]);

        foreach ($invoice->items as $item) {
            $service = $item->service;

            if (!$service || $service->type !== 'course') {
                continue;
            }

            $enrollment = Enrollment::firstOrCreate(
                [
                    'customer_id' => $invoice->customer_id,
                    'service_id' => $service->id,
                ],
                [
                    'invoice_id' => $invoice->id,
                    'status' => 'active',
                    'is_paid' => false,
                    'amount_paid' => 0,
                    'enrolled_at' => now(),
                    'starts_at' => now(),
                ]
            );

            foreach ($service->sessions as $session) {
                $enrollment->sessions()->firstOrCreate([
                    'course_session_id' => $session->id,
                ]);
            }

            $enrollment->amount_paid = $invoice->payments()->sum('amount');

            if ($enrollment->amount_paid >= $item->amount) {
                $enrollment->is_paid = true;
                $enrollment->paid_at = now();
            }

            $enrollment->save();
        }
    }
}