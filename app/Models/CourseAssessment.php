<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Service;
use App\Models\CourseSession;
use App\Models\StudentAssessment;

class CourseAssessment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'service_id',
        'course_session_id',
        'title',
        'assessment_type',
        'description',
        'instructions',
        'max_marks',
        'pass_mark',
        'attachment',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'max_marks' => 'decimal:2',
        'pass_mark' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function session()
    {
        return $this->belongsTo(CourseSession::class, 'course_session_id');
    }

    public function studentAssessments()
    {
        return $this->hasMany(StudentAssessment::class);
    }
}