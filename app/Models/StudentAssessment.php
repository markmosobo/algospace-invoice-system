<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\CourseAssessment;
use App\Models\Enrollment;
use App\Models\EnrollmentSession;

class StudentAssessment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'course_assessment_id',
        'enrollment_id',
        'enrollment_session_id',
        'score',
        'percentage',
        'grade',
        'homework_completed',
        'bonus_completed',
        'remarks',
        'attachment',
        'assessment_date',
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'percentage' => 'decimal:2',
        'homework_completed' => 'boolean',
        'bonus_completed' => 'boolean',
        'assessment_date' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function assessment()
    {
        return $this->belongsTo(CourseAssessment::class, 'course_assessment_id');
    }

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function enrollmentSession()
    {
        return $this->belongsTo(
            EnrollmentSession::class,
            'enrollment_session_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function calculatePercentage()
    {
        if (!$this->assessment || $this->assessment->max_marks == 0) {
            return 0;
        }

        return round(
            ($this->score / $this->assessment->max_marks) * 100,
            2
        );
    }

    public function calculateGrade()
    {
        $percentage = $this->calculatePercentage();

        if ($percentage >= 80) {
            return 'Distinction';
        }

        if ($percentage >= 70) {
            return 'Credit';
        }

        if ($percentage >= 50) {
            return 'Pass';
        }

        return 'Needs Improvement';
    }
}