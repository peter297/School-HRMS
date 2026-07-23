<?php

namespace App\Services;

use App\Mail\LeaveApprovedByHr;
use App\Mail\LeaveRejected;
use App\Mail\LeaveSubmitted;
use App\Models\Leaves;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class LeaveNotificationService
{
    /**
     * Create a new class instance.
     */
    public function __construct() {}

    public function notifySubmitted(Leaves $leave): void
    {
        $this->send($leave, new LeaveSubmitted($leave));
    }

    public function notifyApproved(Leaves $leave): void
    {
        $leave->load(['leaveType', 'approvedBy', 'approvals', 'employee']);
        $this->send($leave, new LeaveApprovedByHr($leave));
    }

    public function notifyRejected(Leaves $leave, string $rejectedBy): void
    {
        $leave->load(['leaveType', 'employee']);
        $this->send($leave, new LeaveRejected($leave, $rejectedBy));
    }

    private function send(Leaves $leave, $mailable): void
    {

        Log::info('DEBUG leave id: ' . $leave->id);
        Log::info('DEBUG employee relation: ' . ($leave->employee ? 'loaded' : 'NULL'));
        Log::info('DEBUG employee_id raw: ' . $leave->employee_id);


        $employee = $leave->employee
            ?? \App\Models\Employees::find($leave->employee_id);

        Log::info('DEBUG after fallback: ' . ($employee ? $employee->email : 'STILL NULL'));

        if (!$employee) {
            Log::warning("LeaveNotification: No employee found for leave ID {$leave->id}");
            return;
        }
        $email = null;

        if (!empty($employee->email)) {
            $email = $employee->email;
        } elseif ($employee->user && !empty($employee->user->email)) {
            $email = $employee->user->email;
        }

        if ($email) {
            Log::warning("LeaveNotification: No email found for employee ID {$leave->employee_id}");
            return;
        }

        try {
            Mail::to($email)->send($mailable);
        } catch (\Exception $e) {
            Log::error("LeaveNotification: Failed to send to {$email} — " . $e->getMessage());
        }
    }
}
