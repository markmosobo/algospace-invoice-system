<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CyberRequestFile;
use App\Models\Service;
use App\Models\Invoice;

class CyberRequest extends Model
{
    protected $fillable = [
        'service',
        'message',
        'delivery_method',
        'urgency',

        'name',
        'email',
        'phone',

        // STATUS
        'status',

        // PAYMENT FIELDS (NEW)
        'payment_type',
        'payment_status',
        'amount',
        'payment_reference',
        'paid_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function files()
    {
        return $this->hasMany(CyberRequestFile::class);
    }

    public function isPaid()
    {
        return $this->payment_status === 'paid';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isPrepay(): bool
    {
        return $this->payment_type === 'prepay';
    }    

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }    
}