<?php

namespace App\Services;

use App\Models\LeaveApproval;
use App\Models\LeaveBalances;
use App\Models\Leaves;
use App\Models\LeaveTypes;
use Carbon\CarbonPeriod;

class LeaveService
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private LeaveNotificationService $notify
    )
    {
        
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

public function notifySubmitted(Leaves $leave): void
{
    $leave->load(['leaveType', 'employee']);
    $this->notify->notifySubmitted($leave);
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

// The Line Manager Approves the Leave that is applied by the employee

public function lineManagerApprove(Leaves $leave, int $actedBy, string $notes = '', string $taskAssignedTo = '', string $taskDescription = '') : void{

            LeaveApproval::updateOrCreate(
                    ['leave_id' => $leave->id, 'stage' => 'line_manager'],
                    [
                        'action' => 'approved',
                        'acted_by' => $actedBy,
                        'notes' => $notes ? : null,
                        'task_assigned_to' => $taskAssignedTo ?: null,
                        'task_description' => $taskDescription ?: null,
                        'acted_at' => now(),
                    ]


            );

             $leave->update([
                        'approval_stage' => 'pending_hr',
                        'status' => 'pending',
                    ]);

}

// Line Manager Rejects the Leave

public function lineManagerReject(Leaves $leave, int $actedBy, string $notes): void{

    LeaveApproval::updateOrCreate(
        ['leave_id' => $leave->id, 'stage' => 'line_manager'],
        [
            'action' => 'rejected',
            'acted_by' => $actedBy,
            'notes' =>$notes,
            'acted_at' => now(),
        ]
    );

    $leave->update([
        'approval_stage' => 'rejected_line_manager',
        'status' => 'rejected',
        'rejection_reason' => $notes,
    ]);

}

// Hr Overides Line Manager Approval

public function hrOverride(Leaves $leave, int $actedBy, string $notes): void{
    LeaveApproval::create([
        'leave_id' => $leave->id,
        'stage' => 'hr',
        'action' => 'overriden',
        'acted_by' => $actedBy,
        'notes' => $notes,
        'acted_at' => now(),
            ]);

            $year = $leave->start_date->year;
            $balance = $this->getOrCreateBalance($leave->employee_id, $leave->leave_type_id, $year);
            $balance->increment('used_days', $leave->days_requested);

            $leave->update([
                'approval_stage' => 'approved',
                'status' => 'approved',
                'approved_by' => $actedBy,
                'approved_at' => now(),
                'rejection_reason' => null,
            ]);

            $this->notify->notifyApproved($leave->fresh(['leaveType', 'approvedBy', 'approvals', 'employee']));
}

public function hrApprove(Leaves $leave, int $actedBy, string $notes = ''): void{
    LeaveApproval::updateOrCreate(
        ['leave_id' => $leave->id, 'stage' => 'hr'],

        [
            'action' => 'approved',
            'acted_by' => $actedBy,
            'notes' => $notes ?: null,
            'acted_at' => now(),

        ]


    );
    $year = $leave->start_date->year;
    $balance = $this->getOrCreateBalance($leave->employee_id, $leave->leave_type_id, $year);
    $balance->increment('used_days', $leave->days_requested);

    $leave->update([
        'approval_stage' => 'approved',
        'status' => 'approved',
        'approved_by' => $actedBy,
        'approved_at' => now(),
    ]);

    $this->notify->notifyApproved($leave->fresh(['leaveType', 'approvedBy', 'approvals', 'employee']));
}

// Hr Rejects the Leave

public function hrReject(Leaves $leave, int $actedBy, string $notes): void{
    LeaveApproval::updateOrCreate(
        ['leave_id' => $leave->id, 'stage' => 'hr'],
        [
            'action' => 'rejected',
            'acted_by' => $actedBy,
            'notes' => $notes,
            'acted_at' => now(),
        ]
    );

    $leave->update([
        'approval_stage' => 'rejected_hr',
        'status' => 'rejected',
        'rejection_reason' => $notes,
    ]);

     $this->notify->notifyRejected(
            $leave->fresh(['leaveType', 'employee']),
            'HR'
        );
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
