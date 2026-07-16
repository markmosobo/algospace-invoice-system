<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CourseSession;

class CourseSessionTopic extends Model
{
    protected $fillable = [
        'course_session_id',
        'title',
        'description',
        'sort_order',
    ];

    public function session()
    {
        return $this->belongsTo(CourseSession::class);
    }
}