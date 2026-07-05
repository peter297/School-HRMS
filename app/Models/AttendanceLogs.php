<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceLogs extends Model
{
    /** @use HasFactory<\Database\Factories\AttendanceLogsFactory> */
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'date',
        'check_in',
        'check_out',
        'source',
        'import_batch',
    ];

    protected $casts = [
        'date' => 'date: Y-m-d',
        'check_in' => 'string',
        'check_out' => 'string',
    ];

    public function employee()
    {
        return $this->belongsTo(Employees::class);
    }

    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecords::class);
    }
}
