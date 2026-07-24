<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Enrollment;

class CourseCertificate extends Model
{

    protected $fillable = [

        'enrollment_id',

        'certificate_no',

        'percentage',

        'grade',

        'issued_date',

        'issued_by',

        'file_path'

    ];


    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

}
