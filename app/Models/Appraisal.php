<?php
// app/Models/Appraisal.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Employees;
use App\Models\User;
class Appraisal extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'employee_id',
        'type',
        'period',
        'year',
        'status',
        'line_manager_comments',
        'hr_comments',
        'recommendations',
        'overall_score',
        'overall_grade',
        'submitted_by',
        'submitted_at',
        'approved_by',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'approved_at'  => 'datetime',
            'overall_score'=> 'decimal:2',
        ];
    }

    public function employee()
    {
        return $this->belongsTo(Employees::class, 'employee_id');
    }

    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function kpis()
    {
        return $this->hasMany(AppraisalKpi::class)->orderBy('sort_order');
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'annual'    => 'Annual appraisal',
            'mid_year'  => 'Mid-year review',
            'probation' => 'Probation review',
            default     => ucfirst($this->type),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending'   => 'zinc',
            'submitted' => 'yellow',
            'approved'  => 'green',
            'rejected'  => 'red',
            default     => 'zinc',
        };
    }

    public function getGradeLabelAttribute(): string
    {
        return match($this->overall_grade) {
            'exceeds_expectations' => 'Exceeds expectations',
            'meets_expectations'   => 'Meets expectations',
            'below_expectations'   => 'Below expectations',
            default                => '—',
        };
    }

    public function getGradeColorAttribute(): string
    {
        return match($this->overall_grade) {
            'exceeds_expectations' => 'green',
            'meets_expectations'   => 'blue',
            'below_expectations'   => 'red',
            default                => 'zinc',
        };
    }

    public function calculateOverallScore(): array
    {
        $kpis = $this->kpis()->whereNotNull('score')->get();

        if ($kpis->isEmpty()) {
            return ['score' => null, 'grade' => null];
        }

        $avg = $kpis->avg('score');

        $grade = match(true) {
            $avg >= 2.5 => 'exceeds_expectations',
            $avg >= 1.5 => 'meets_expectations',
            default     => 'below_expectations',
        };

        return ['score' => round($avg, 2), 'grade' => $grade];
    }
}