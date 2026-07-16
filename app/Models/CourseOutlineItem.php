<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CourseOutline;

class CourseOutlineItem extends Model
{
    protected $fillable = [
        'course_outline_id',
        'section',
        'title',
        'description',
        'sort_order',
    ];

    public function outline()
    {
        return $this->belongsTo(CourseOutline::class, 'course_outline_id');
    }
}