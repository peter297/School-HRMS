<?php
// app/Models/Termination.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Termination extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'employee_id',
        'termination_date',
        'notice_date',
        'last_working_day',
        'reason',
        'reason_details',
        'status',
        'notice_served',
        'notice_served_date',
        'clearance_done',
        'clearance_done_date',
        'final_pay_processed',
        'final_pay_processed_date',
        'supporting_document',
        'hr_notes',
        'recorded_by',
    ];

    protected function casts(): array
    {
        return [
            'termination_date'         => 'date',
            'notice_date'              => 'date',
            'last_working_day'         => 'date',
            'notice_served'            => 'boolean',
            'notice_served_date'       => 'date',
            'clearance_done'           => 'boolean',
            'clearance_done_date'      => 'date',
            'final_pay_processed'      => 'boolean',
            'final_pay_processed_date' => 'date',
        ];
    }

    public function employee()
    {
        return $this->belongsTo(Employees::class, 'employee_id');
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function getReasonLabelAttribute(): string
    {
        return match($this->reason) {
            'misconduct'   => 'Misconduct',
            'redundancy'   => 'Redundancy',
            'contract_end' => 'Contract end',
            'performance'  => 'Performance',
            'absenteeism'  => 'Absenteeism',
            'other'        => 'Other',
            default        => ucfirst($this->reason),
        };
    }

    public function getReasonColorAttribute(): string
    {
        return match($this->reason) {
            'misconduct'   => 'red',
            'redundancy'   => 'yellow',
            'contract_end' => 'blue',
            'performance'  => 'orange',
            'absenteeism'  => 'yellow',
            'other'        => 'zinc',
            default        => 'zinc',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'initiated'    => 'Initiated',
            'notice_served'=> 'Notice served',
            'clearance_done' => 'Clearance done',
            'completed'    => 'Completed',
            default        => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'initiated'      => 'red',
            'notice_served'  => 'yellow',
            'clearance_done' => 'blue',
            'completed'      => 'green',
            default          => 'zinc',
        };
    }

    public function getCompletionPercentageAttribute(): int
    {
        $stages = [
            true, // initiated is always done
            $this->notice_served,
            $this->clearance_done,
            $this->final_pay_processed,
        ];

        $done = count(array_filter($stages));
        return (int) round(($done / 4) * 100);
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['completed']);
    }
}
