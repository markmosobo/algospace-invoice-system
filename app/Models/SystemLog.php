<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class SystemLog extends Model
{
    protected $fillable = [
        'user_id',
        'description',
        'ip_address'
    ]; 
    
    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }     
}
