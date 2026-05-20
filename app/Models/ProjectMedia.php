<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Project;

class ProjectMedia extends Model
{
    protected $fillable = [
        'project_id',
        'file_path',
        'file_name',
        'type',
        'caption',
        'notes',
        'stage',
        'uploaded_by'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}