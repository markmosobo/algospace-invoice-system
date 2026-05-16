<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CyberRequestFile;
use App\Models\Service;
use App\Models\Invoice;

class CyberRequest extends Model
{
    protected $fillable = [
        'service_id',
        'message',
        'delivery_method',
        'urgency',

        'name',
        'email',
        'phone',

        // STATUS FLOW
        'status',

        // PAYMENT FLOW
        'payment_type',
        'payment_status',
        'amount',
        'payment_reference',
        'paid_at',

        // 🆕 TIMESTAMPS (IMPORTANT)
        'billed_at',
        'completed_at',

        // 🆕 OPTIONAL DIRECT LINK (if you still use it in some places)
        'invoice_id',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'billed_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function files()
    {
        return $this->hasMany(CyberRequestFile::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class, 'source_id')
            ->where('source', 'cyber_request');
    }

    // -------------------------
    // STATUS HELPERS
    // -------------------------

    public function isBilled(): bool
    {
        return $this->status === 'billed';
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isPrepay(): bool
    {
        return $this->payment_type === 'prepay';
    }

    public function hasInvoice(): bool
    {
        return $this->invoice()->exists();
    }
}