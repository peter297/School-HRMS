<?php

namespace App\Livewire\Staff;

use App\Models\LeaveBalances;
use App\Models\Leaves;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Dashboard extends Component
{
    #[Layout('componets.layouts.staff')]
    public function render()
    {
        $employee = auth()->user()->employee;

        $myLeaves = Leaves::where('employee_id', $employee?->id)
            ->where('year', now()->year)
            ->with('leaveType')
            ->get();

        $balances = LeaveBalances::where('employee_id', $employee?->id)
            ->where('year', now()->year)
            ->with('leaveType')
            ->get();

        $pendingApprovals = $employee
            ? Leaves::whereHas('employee', fn($q) => 
                $q->where('line_manager_id', $employee->id)
                )
                ->where('approval_stage', 'pending_line_manager')
                ->count()
                : 0;
        return view('livewire.staff.dashboard', compact('myLeaves', 'balances', 'pendingApprovals', 'employee'));
    }
}
