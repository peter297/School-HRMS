<?php

namespace App\Models;

use Carbon\Carbon;
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
        'qualification',
        'tsc_number',
        'bank_code',
        'branch_code',
        'date_of_joining',
        'gender',
        'national_id',
        'kra_pin',
        'nssf_number',
        'sha_number',
        'bank_name',
        'bank_account_number',
        'employment_status',
        'date_of_birth',
        'user_id',
        'line_manager_id',
        'age',
        'years_of_employment',
    ];

    #[Override]
    protected function casts()
    {
        return[
            'date_of_joining' => 'date',
            'date_of_birth' => 'date',
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
        return $this->hasMany(Incident::class);
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

    public function getAgeAttribute(): int{
        return Carbon::parse($this->date_of_birth)->diffInYears(Carbon::now());
    }

    public function getYearsOfEmploymentAttribute(): int{
        return Carbon::parse($this->date_of_joining)->diffInYears(Carbon::now());
    }

    public function lineManager(){
        return $this->belongsTo(Employees::class, 'line_manager_id');
    }

    public function directReports(){
        return $this->hasMany(Employees::class, 'line_manager_id');
    }







}
