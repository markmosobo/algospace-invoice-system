<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;
    public $pdf;

    public function __construct($invoice, $pdf)
    {
        $this->invoice = $invoice;
        $this->pdf = $pdf;
    }

    public function build()
    {
        return $this->subject('Your Invoice from AlgoSpace Cyber')
            ->view('emails.invoice') // ✅ IMPORTANT FIX (NOT invoices.invoice)
            ->attachData($this->pdf, "invoice-{$this->invoice->invoice_number}.pdf", [
                'mime' => 'application/pdf',
            ]);
    }
}