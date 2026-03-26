<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class Todo extends Model
{
    use HasFactory, SoftDeletes;

    // Mass assignable fields
    protected $fillable = [
        'title',
        'description',
        'category',
        'priority',
        'status',
        'checked_at',
        'delegated_to',
    ];

    /**
     * Check if task is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if task is delegated
     */
    public function isDelegated(): bool
    {
        return $this->status === 'delegated' && !is_null($this->delegated_to);
    }

    /**
     * Check if task is deferred
     */
    public function isDeferred(): bool
    {
        return $this->status === 'deferred';
    }

    /**
     * Check if task is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Relationship: task delegated user (staff)
     */
    public function delegatedUser()
    {
        return $this->belongsTo(User::class, 'delegated_to');
    }

    /**
     * Mark task as checked (counter-checked)
     */
    public function markChecked()
    {
        $this->checked_at = now();
        if ($this->status !== 'completed') {
            $this->status = 'completed';
        }
        $this->save();
    }

    /**
     * Delegate task to a staff member
     */
    public function delegateTo($userId)
    {
        $this->status = 'delegated';
        $this->delegated_to = $userId;
        $this->save();
    }

    /**
     * Defer task
     */
    public function deferTask()
    {
        $this->status = 'deferred';
        $this->save();
    }
}