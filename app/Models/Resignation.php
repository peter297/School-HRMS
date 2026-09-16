<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Resignation extends Model
{
     use SoftDeletes;

    protected $fillable = [
        'employee_id',
        'resignation_date',
        'notice_end_date',
        'last_working_day',
        'notice_period',
        'reason',
        'reason_details',
        'status',
        'notice_served',
        'notice_served_date',
        'last_working_day_confirmed',
        'last_working_day_confirmed_date',
        'clearance_done',
        'clearance_done_date',
        'final_pay_processed',
        'final_pay_processed_date',
        'notes',
        'recorded_by',
    ];

    protected function casts(): array
    {
        return [
            'resignation_date'             => 'date',
            'notice_end_date'              => 'date',
            'last_working_day'             => 'date',
            'notice_served'                => 'boolean',
            'notice_served_date'           => 'date',
            'last_working_day_confirmed'   => 'boolean',
            'last_working_day_confirmed_date' => 'date',
            'clearance_done'               => 'boolean',
            'clearance_done_date'          => 'date',
            'final_pay_processed'          => 'boolean',
            'final_pay_processed_date'     => 'date',
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

    public function getNoticePeriodLabelAttribute(): string
    {
        return match($this->notice_period) {
            '1_week'   => '1 Week',
            '2_weeks'  => '2 Weeks',
            '1_month'  => '1 Month',
            '2_months' => '2 Months',
            '3_months' => '3 Months',
            default    => ucfirst($this->notice_period),
        };
    }

    public function getReasonLabelAttribute(): string
    {
        return match($this->reason) {
            'personal'          => 'Personal reasons',
            'better_opportunity'=> 'Better opportunity',
            'relocation'        => 'Relocation',
            'health'            => 'Health reasons',
            'further_studies'   => 'Further studies',
            'retirement'        => 'Retirement',
            'other'             => 'Other',
            default             => ucfirst($this->reason),
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'notice_served'       => 'Notice served',
            'last_working_day'    => 'Last working day set',
            'clearance_done'      => 'Clearance done',
            'final_pay_processed' => 'Final pay processed',
            'completed'           => 'Completed',
            default               => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'notice_served'       => 'yellow',
            'last_working_day'    => 'blue',
            'clearance_done'      => 'purple',
            'final_pay_processed' => 'green',
            'completed'           => 'green',
            default               => 'zinc',
        };
    }

    public function getCompletionPercentageAttribute(): int
    {
        $stages = [
            $this->notice_served,
            $this->last_working_day_confirmed,
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
