<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Service;
use App\Models\CourseOutlineItem;

class CourseOutline extends Model
{
    protected $fillable = [
        'service_id',
        'overview',
        'certificate_information',
        'notes',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function items()
    {
        return $this->hasMany(CourseOutlineItem::class)
            ->orderBy('sort_order');
    }
}