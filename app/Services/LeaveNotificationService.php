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
    // Always do a fresh direct lookup — bypass relationship caching
    $employee = \App\Models\Employees::find($leave->employee_id);

    if (!$employee) {
        Log::warning("LeaveNotification: No employee found for leave ID {$leave->id}");
        return;
    }

    // Get email — check employee record then linked user
    $email = null;

    if (!empty(trim((string) $employee->email))) {
        $email = trim((string) $employee->email);
    } else {
        $user = \App\Models\User::find($employee->user_id);
        if ($user && !empty(trim((string) $user->email))) {
            $email = trim((string) $user->email);
        }
    }

    if (!$email) {
        Log::warning("LeaveNotification: No email found for employee ID {$employee->id} ({$employee->first_name} {$employee->last_name})");
        return;
    }

    try {
        Mail::to($email)->send($mailable);
        Log::info("LeaveNotification: Sent to {$email} for {$employee->staff_number}");
    } catch (\Exception $e) {
        Log::error("LeaveNotification: Failed to send to {$email} — " . $e->getMessage());
    }
}
}
