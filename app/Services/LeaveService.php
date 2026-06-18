<?php

namespace App\Services;

use App\Models\LeaveBalances;
use App\Models\Leaves;
use App\Models\LeaveTypes;
use Carbon\CarbonPeriod;

class LeaveService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    // Count working Days (Mon-Fri) between two date inclusive

    public function countWorkingDays(string $start, string $end){
        $period = CarbonPeriod::create($start, $end);
        $count = 0;

        foreach ($period as $date) {
            if (!$date->isWeekend()) {
                $count++;
            }
        }

        return $count;
    }

/**
 * Get or create leave balance record for employee/type/year
 */

public function getOrCreateBalance(int $employeeId, int $leaveTypeId, int $year){

    $leaveType = LeaveTypes::find($leaveTypeId);

    return LeaveBalances::firstOrCreate(
        [
            'employee_id' => $employeeId,
            'leave_type_id' => $leaveTypeId,
            'year' => $year
        ],
        [
            'entitled_days' => $leaveType->days_allowed,
            'used_days' => 0,
        ]
    );



}

/**
 * Check if employee has enough Balance.
 */

public function hasBalance(int $employeeId, int $leaveTypeId, int $days, int $year ): bool
{
   $balance = $this->getOrCreateBalance($employeeId, $leaveTypeId, $year);
   return $balance->remaining_days >= $days;
}

public function approve(Leaves $leave, int $approvedBy){
    $year = $leave->start_date->year;
    $balance = $this->getOrCreateBalance($leave->employee_id, $leave->leave_type_id, $year);

    $balance->increment('used_days', $leave->days_requested);

    $leave->update([
        'status' => 'approved',
        'approved_by' => $approvedBy,
        'approved_at' => now(),
    ]);

    
}

/**
 * Reject a leave application
 */

public function reject(Leaves $leave, string $reason, int $rejectedBy){
    $leave->update([
        'status' => 'rejected',
        'rejected_reason' => $reason,
        'rejected_by' => $rejectedBy,
        'rejected_at' => now(),
        
    ]);

    
   


}

 public function cancel(Leaves $leave){
        if($leave->status == 'approved') {
            $year = $leave->start_date->year;
            $balance = $this->getOrCreateBalance($leave->employee_id, $leave->leave_type_id, $year);
            $balance->decrement('used_days', $leave->days_requested);
        }

        $leave->update([
            'status' => 'cancelled'
        ]);

}

}