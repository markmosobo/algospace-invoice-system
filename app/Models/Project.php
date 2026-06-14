<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\ProjectMedia;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'type',
        'board_type',
        'status',
        'start_date',
        'end_date',
        'due_date',
        'current_stage',
        'cover_image',
        'blocker',
        'priority',
        'progress',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'due_date'   => 'date',
    ];

    /* =====================
     |  Relationships
     ===================== */

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function media()
    {
        return $this->hasMany(ProjectMedia::class)
        ->orderBy('id', 'asc');
    }

    public const STAGE_PROGRESS_MAP = [
        'ideation'    => 10,
        'planning'    => 25,
        'setup'       => 40,
        'execution'   => 70,
        'completion'  => 100,
    ];

    public function calculateProgressFromStage(string $stage): int
    {
        return self::STAGE_PROGRESS_MAP[$stage] ?? 0;
    }


    /* =====================
     |  Scopes (VERY USEFUL)
     ===================== */

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeBlocked($query)
    {
        return $query->where('status', 'blocked');
    }

    public function scopeAdminBoard($query)
    {
        return $query->where('board_type', 'admin');
    }

    public function scopePublicBoard($query)
    {
        return $query->where('board_type', 'public');
    }
}