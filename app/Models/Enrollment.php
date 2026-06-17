<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Customer;
use App\Models\Service;

class Enrollment extends Model
{
    protected $fillable = [
        'customer_id',
        'service_id',
        'enrolled_at',
        // STATUS FLOW
        'status',

        // PAYMENT TRACKING
        'is_paid',
        'amount_paid',
        'paid_at',

        // PROGRESS
        'progress_percent',

        // DATES
        'enrolled_at',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'is_paid' => 'boolean',
        'amount_paid' => 'decimal:2',
        'progress_percent' => 'integer',
        'enrolled_at' => 'datetime',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'paid_at' => 'datetime',
    ];    

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }   
    
    public function markAsPaid($amount = null)
    {
        $this->is_paid = true;

        if ($amount !== null) {
            $this->amount_paid = $amount;
        }

        $this->paid_at = now();
        $this->status = 'active';

        $this->save();
    }

    public function complete()
    {
        $this->status = 'completed';
        $this->progress_percent = 100;
        $this->ends_at = now();
        $this->save();
    }

    public function activate()
    {
        $this->status = 'active';

        if (!$this->starts_at) {
            $this->starts_at = now();
        }

        $this->save();
    }    
}
