<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermittedExits extends Model
{
    /** @use HasFactory<\Database\Factories\PermittedExitsFactory> */
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'date',
        'exit_time',
        'return_time',
        'reason',
        'recorded_by',
    ];

    protected $casts = [
        'date' => 'date',
        'exit_time' => 'datetime:H:i:s',
        'return_time' => 'datetime:H:i:s',
    ];

    public function employee()
    {
        return $this->belongsTo(Employees::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function getDurationMinutesAttribute(): ?int
    {
        if (!$this->return_time && !$this->exit_time) {
            return null;
        }
       $exit = \Carbon\Carbon::parse($this->exit_time);
       $return = \Carbon\Carbon::parse($this->return_time);
       return $exit->diffInMinutes($return);
    }
}
