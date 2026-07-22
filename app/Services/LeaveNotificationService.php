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
    public function __construct()
    {
        
    }

    public function notifySubmitted(Leaves $leave): void{
            $this->send($leave, new LeaveSubmitted($leave));
    }

    public function notifyApproved(Leaves $leave): void
    {
        $leave->load(['leaveType', 'approvedBy', 'approvals', 'employee']);
        $this->send($leave, new LeaveApprovedByHr($leave));
    }

    public function notifyRejected(Leaves $leave, string $rejectedBy): void{
        $leave->load(['leaveType', 'employee']);
        $this->send($leave, new LeaveRejected($leave, $rejectedBy));
    }

    private function send(Leaves $leave, $mailable): void
    {
        $email = $leave->employee?->email ?? $leave->employee?->user?->email;

        if($email){
            Log::warning("LeaveNotofication: No email found for employee ID {$leave->employee_id}");
            return;
        }

        try{
            Mail::to($email)->send($mailable);

        }catch(\Exception $e){
            Log::error("LeaveNotification: Failed to send to {$email} — " . $e->getMessage());
        }
    }
}
