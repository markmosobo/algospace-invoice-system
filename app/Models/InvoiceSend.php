<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Invoice;

class InvoiceSend extends Model
{
    protected $fillable = [
        'invoice_id',
        'channel',
        'status',
        'sent_at'
    ];
    
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }    
}
