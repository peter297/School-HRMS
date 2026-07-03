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
        'branch',
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

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function leaves()
    {
        return $this->hasMany(Leaves::class);
    }

    public function attendanceLogs()
    {
        return $this->hasMany(AttendanceLogs::class);
    }

    public function permittedExits()
    {
        return $this->hasMany(PermittedExits::class);
    }

    public function incidents()
    {
        return $this->hasMany(Incidents::class);
    }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getBranchLabelAttribute(): string{

    return match($this->branch){
        'juja_road' => 'Juja Road',
        'kitisuru' => 'Kitisuru',
        'south_c' => 'South C',
        default => ucfirst($this->branch),

    };
    }








}
