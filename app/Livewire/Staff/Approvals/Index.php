<?php

namespace App\Livewire\Staff\Approvals;

use App\Models\LeaveApproval;
use App\Models\Leaves;
use App\Services\LeaveService;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $filterStage = 'pending_line_manager';

    // Approve Modal

    public bool $showApproveModal = false;
    public int $approvingId = 0;
    public string $approveNotes = '';
    public string $taskAssignedTo = '';
    public string $taskDescription = '';

    // Reject Modal

    public bool $showRejectModal = false;
    public int $rejectingId = 0;
    public string $rejectNotes = '';

    public function openApproveModal(int $id): void{
        $this->approvingId = $id;
        $this->approveNotes = '';
        $this->taskAssignedTo = '';
        $this->taskDescription = '';
        $this->showApproveModal = true;
    }

    public function openRejectModal(int $id): void{
        $this->rejectingId = $id;
        $this->rejectNotes = '';
        $this->showRejectModal = true;
    }

    public function confirmApprove(): void{
        $this->validate([
           'taskAssignedTo' => 'required|string|max:255',
           'taskDescription' => 'nullable|string|max:1000',
           'approveNotes'  => 'nullable|string|max:500',
        ]);

        $leave = $this->getDirectReportLeave($this->approvingId);
        $service = app(LeaveService::class);

        if(!$service->hasBalance($leave->employee_id, $leave->leave_type_id, $leave->days_requested, $leave->start_date->year )){
            $this->addError('taskAssignedTo', 'Employee has insufficinet leave balance.');
            return;

        }

        $service->lineManagerApprove(
            $leave,
            auth()->id(),
            $this->approveNotes,
            $this->taskAssignedTo,
            $this->taskDescription
        );

        $this->showApproveModal = false;
        session()->flash('success', 'Leave approved and forwarded to HR');
    }

    public function confirmReject(): void{
        $this->validate(['rejectNotes' => 'required|string|min:5']);

        $leave = $this->getDirectReportLeave($this->rejectingId);
        app(LeaveService::class)->lineManagerReject($leave, auth()->id(), $this->rejectNotes);

        $this->showRejectModal = false;
        session()->flash('success', 'Leave rejected. HR has been notified.');
    }

    private function getDirectReportLeave(int $leaveId): Leaves{
        $employee = auth()->user()->employee;

        return Leaves::whereHas('employee', fn($q) => 
            $q->where('line_manager_id', $employee->id)
            ->findOrFail($leaveId));
    }




    #[Layout('components.layouts.staff')]
    public function render()
    {
        $employee = auth()->user()->employee;
          $leaves = Leaves::with(['employee', 'leaveType', 'approvals'])
          ->whereHas('employee', fn($q) => 
            $q->where('line_manager_id', $employee?->id)
            )
            ->when($this->filterStage, fn($q) => 
              $q->where('approval_stage', $this->filterStage)
              )
              ->latest()
              ->paginate(15);
        return view('livewire.staff.approvals.index', compact('leaves'));
    }
}
