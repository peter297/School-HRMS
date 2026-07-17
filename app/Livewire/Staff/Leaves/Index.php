<?php

namespace App\Livewire\Staff\Leaves;

use App\Models\Leaves;
use App\Services\LeaveService;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public function cancelLeave(int $id): void{
        $employee = auth()->user()->employee;

        $leave = Leaves::where('id', $id)
            ->where('employee_id', $employee?->id)
            ->firstOrFail();

            if(in_array($leave->approval_stage, ['pending_line_manager', 'pending_hr', 'approved'])){
                session()->flash('error', 'This Leave cannot be cancelled.');
                return;
            }

            app(LeaveService::class)->cancel($leave);
            session()->flash('success', 'Leave cancelled successfully');
    }

    #[Layout('componets.layouts.staff')]
    public function render()
    {
        $employee = auth()->user()->employee;

        $leaves = Leaves::where('employee_id', $employee?->id)
            ->with(['leaveType', 'approvals.actedBy'])
            ->latest()
            ->paginate(10);
        return view('livewire.staff.leaves.index', compact('leaves'));
    }
}
