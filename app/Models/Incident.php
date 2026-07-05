<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Incident extends Model
{
    /** @use HasFactory<\Database\Factories\IncidentsFactory> */
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'date',
        'type',
        'minutes_late',
        'minutes_early',
        'details',
        'resolved',
        'resolution_notes',
        'resolved_by',
        'resolved_at',
    ];

    protected $casts = [
        'date' => 'date',
        'resolved' => 'boolean',
        'resolved_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employees::class);
    }

    public function resolvedBy()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function getTypeLabelAttribute()
    {
        return match ($this->type) {
            'late_arrival' => 'Late Arrival',
            'early_departure' => 'Early Departure',
            'absent' => 'Absent',
            'late_and_early' => 'Late and Early',
            default => ucfirst($this->type),
        };
    }

    public function scopeUnresolved($query)
    {
        return $query->where('resolved', false);
    }
}
