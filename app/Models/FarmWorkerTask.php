<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\FarmVenture;
use App\Models\FarmWorker;

class FarmWorkerTask extends Model
{
    protected $table = 'farm_worker_tasks';

    protected $fillable = [
        'worker_id',
        'venture_id',
        'task',
        'work_date',
        'amount_paid',
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'work_date' => 'date',
    ];

    // Relationship: which worker did this task
    public function worker()
    {
        return $this->belongsTo(FarmWorker::class, 'worker_id');
    }

    // Relationship: which venture this task belongs to
    public function venture()
    {
        return $this->belongsTo(FarmVenture::class, 'venture_id');
    }    
}
