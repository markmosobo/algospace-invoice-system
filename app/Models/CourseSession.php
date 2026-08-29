<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Service;
use App\Models\CourseSessionTopic;
use App\Models\CourseMaterial;
use App\Models\CourseAssessment;

class CourseSession extends Model
{
    protected $fillable = [
        'service_id',
        'session_number',
        'title',
        'description',
        'duration_hours',
        'sort_order',
    ];

    protected $casts = [
        'duration_hours' => 'decimal:2',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function topics()
    {
        return $this->hasMany(CourseSessionTopic::class)
            ->orderBy('sort_order');
    }

    public function materials()
    {
        return $this->hasMany(
            CourseMaterial::class,
            'course_session_id'
        );
    }

    public function assessments()
    {
        return $this->hasMany(
            CourseAssessment::class,
            'course_session_id'
        )->orderBy('sort_order');
    }    
}