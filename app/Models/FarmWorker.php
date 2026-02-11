<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\FarmWorkerTask;

class FarmWorker extends Model
{
    protected $table = 'farm_workers';

    protected $fillable = [
        'name',
        'role',
        'phone',
        'daily_rate',
        'status',
    ];

    protected $casts = [
        'daily_rate' => 'decimal:2',
    ];

    // Relationship: a worker can have many tasks
    public function tasks()
    {
        return $this->hasMany(FarmWorkerTask::class, 'worker_id');
    }    
}
