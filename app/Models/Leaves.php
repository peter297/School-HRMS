<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Leaves extends Model
{
    /** @use HasFactory<\Database\Factories\LeavesFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'leave_type_id',
        'start_date',
        'end_date',
        'days_requested',
        'status',
        'approval_stage',
        'reason',
        'rejection_reason',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employees::class, 'employee_id');
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveTypes::class, 'leave_type_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function approvals(){
        return $this->hasMany(LeaveApproval::class, 'leave_id');
    }

    public function lineManagerApproval(){
        return $this->hasOne(LeaveApproval::class, 'leave_id')->with('stage', 'line_manager');
    }

    public function hrApproval(){
        return $this->hasOne(LeaveApproval::class, 'leave_id')->with('stage', 'hr');
    }

    public function getStatusColorAttribute()
    {
        return match ($this->status) {

            'approved' => 'green',
            'rejected_line_manager',
            'rejected_hr' => 'red',
            'pending_line_manager' => 'yellow',
            'pending_hr' => 'blue',
            'cancelled' => 'zinc',
            default => 'zinc',
        };
    }

    public function getApprovalStageLabelAttribute(): string{
        return match($this->approval_stage){
            'pending_line_manager' => 'Awaiting Line Manager',
            'pending_hr' => 'Awaiting HR',
            'approved' => 'Approved',
            'rejected_line_manager' => 'Rejected by Line Manager',
            'rejected_hr' => 'Rejected by HR',
            'cancelled' => 'Cancelled',
             default                 => ucfirst($this->approval_stage),

        };
    }

    public function getDurationLabelAttribute()
    {
        return $this->days_requested === 1 ? '1 day' : "{$this->days_requested} days";
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }


}
