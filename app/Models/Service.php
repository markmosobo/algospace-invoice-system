<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\InvoiceItem;
use App\Models\Enrollment;

class Service extends Model
{
    protected $fillable = [
        'id',
        'name',
        'category',
        'type',           // service | course
        'tier',           // beginner | intermediate | advanced | refresher
        'price',
        'payment_type',
        'unit',
        'duration_units', // number of Saturdays
        'schedule_type',  // saturday | weekday | custom
        'session_hours',  // hours per session
        'is_bundle',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'duration_units' => 'float',
        'session_hours' => 'float',
        'is_bundle' => 'boolean',
    ];

    // Relationships
    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class);
    } 
    
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    } 
    
    public function scopeCourses($query)
    {
        return $query->where('type', 'course');
    }

    public function scopeSaturday($query)
    {
        return $query->where('schedule_type', 'saturday');
    }

    public function scopeRefreshers($query)
    {
        return $query->where('tier', 'refresher');
    }  
    
    public function getDurationLabelAttribute()
    {
        if (!$this->duration_units) {
            return null;
        }

        if ($this->duration_units == 0.5) {
            return 'Half Saturday';
        }

        if ($this->duration_units == 1) {
            return '1 Saturday';
        }

        return $this->duration_units . ' Saturdays';
    }

    public function getTotalHoursAttribute()
    {
        if (!$this->duration_units || !$this->session_hours) {
            return null;
        }

        return $this->duration_units * $this->session_hours;
    }

    public function getTierImageAttribute()
    {
        return [
            'basic'     => 'courses/basic.webp',
            'practical' => 'courses/practical.webp',
            'refresher' => 'courses/refresher.webp',
            'coding'    => 'courses/coding.webp',
        ][$this->tier] ?? 'courses/default.webp';
    }

}
