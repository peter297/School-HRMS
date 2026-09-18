<?php
// app/Models/CasualIntern.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CasualIntern extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'staff_number',
        'first_name',
        'last_name',
        'email',
        'phone',
        'national_id',
        'type',
        'division',
        'branch',
        'job_title',
        'supervisor',
        'start_date',
        'end_date',
        'daily_rate',
        'institution',
        'course',
        'status',
        'promoted',
        'promoted_date',
        'promoted_to_employee_id',
        'notes',
        'recorded_by',
    ];

    protected function casts(): array
    {
        return [
            'start_date'    => 'date',
            'end_date'      => 'date',
            'promoted'      => 'boolean',
            'promoted_date' => 'date',
            'daily_rate'    => 'decimal:2',
        ];
    }

    public function promotedToEmployee()
    {
        return $this->belongsTo(Employees::class, 'promoted_to_employee_id');
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'casual' => 'Casual',
            'intern' => 'Intern',
            default  => ucfirst($this->type),
        };
    }

    public function getTypeColorAttribute(): string
    {
        return match($this->type) {
            'casual' => 'blue',
            'intern' => 'purple',
            default  => 'zinc',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'active'     => 'green',
            'completed'  => 'blue',
            'terminated' => 'red',
            default      => 'zinc',
        };
    }

    public function getDivisionLabelAttribute(): string
    {
        return match($this->division) {
            'eye'            => 'Early Years Education',
            'upper_primary'  => 'Upper Primary',
            'junior_school'  => 'Junior School',
            'administration' => 'Administration',
            'support'        => 'Support',
            default          => ucfirst($this->division),
        };
    }

    public function getBranchLabelAttribute(): string
    {
        return match($this->branch) {
            'juja_road' => 'Juja Road',
            'kitisuru'  => 'Kitisuru',
            'south_c'   => 'South C',
            default     => ucfirst($this->branch),
        };
    }

    public function getDurationAttribute(): string
    {
        $end = $this->end_date ?? now();
        return $this->start_date->diffForHumans($end, true);
    }

    public function getDaysWorkedAttribute(): int
    {
        $end = $this->end_date ?? today();
        return $this->start_date->diffInWeekdays($end);
    }

    public function getTotalEarningsAttribute(): ?string
    {
        if (!$this->daily_rate) return null;
        return 'KES ' . number_format($this->daily_rate * $this->days_worked, 2);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCasuals($query)
    {
        return $query->where('type', 'casual');
    }

    public function scopeInterns($query)
    {
        return $query->where('type', 'intern');
    }
}
