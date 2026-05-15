<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CyberRequest;

class CyberRequestFile extends Model
{
    protected $fillable = [
        'cyber_request_id',
        'file_path',
        'file_name',
        'file_type',
    ];  
    
    public function cyberRequest()
    {
        return $this->belongsTo(CyberRequest::class);
    }    
}
