<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CourseSession;
use App\Models\Enrollment;
use App\Models\StudentAssessment;

class EnrollmentSession extends Model
{

protected $fillable=[
    'enrollment_id',
    'course_session_id',
    'completed',
    'completed_at'
];

public function enrollment()
{

    return $this->belongsTo(
    Enrollment::class
    );

}

public function session()
{
    return $this->belongsTo(
        CourseSession::class,
        'course_session_id'
    );
}

public function assessments()
{
    return $this->hasMany(
        StudentAssessment::class,
        'enrollment_session_id'
    );
}


}
