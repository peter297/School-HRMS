<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Override;

class Employees extends Model
{
    /** @use HasFactory<\Database\Factories\EmployeesFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'staff_number',
        'first_name',
        'last_name',
        'email',
        'phone',
        'staff_type',
        'division',
        'job_title',
        'date_of_joining',
        'gender',
        'national_id',
        'employment_status',
    ];

    #[Override]
    protected function casts()
    {
        return[
            'date_of_joining' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // public function contracts()
    // {
    //     return $this->hasMany(Contract::class);
    // }

    // public function leaves()
    // {
    //     return $this->hasMany(Leave::class);
    // }

    // public function attendanceLogs()
    // {
    //     return $this->hasMany(AttendanceLog::class);
    // }

    // public function permittedExits()
    // {
    //     return $this->hasMany(PermittedExit::class);
    // }

    // public function incidents()
    // {
    //     return $this->hasMany(Incident::class);
    // }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }








}
