<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceRecords extends Model
{
    /** @use HasFactory<\Database\Factories\AttendanceRecordsFactory> */
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'attendance_log_id',
        'date',
        'status',
        'minutes_late',
        'minutes_early',
        'flagged',
    ];

    protected $casts = [
        'date' => 'date',
        'flagged' => 'boolean',
    ];

    public function employee()
    {
        return $this->belongsTo(Employees::class);
    }

    public function attendanceLog()
    {
        return $this->belongsTo(AttendanceLogs::class);
    }

    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'present' => 'green',
            'absent' => 'yellow',
            'late' => 'red',
            'early_departure' => 'blue',
            'late_and_early' => 'purple',
            default => 'zinc',
        };
    }
}
