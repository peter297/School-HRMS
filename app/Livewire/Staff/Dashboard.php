<?php

namespace App\Livewire\Staff;

use App\Models\LeaveBalances;
use App\Models\Leaves;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Dashboard extends Component
{
    #[Layout('components.layouts.staff')]
    public function render()
    {
        $user     = auth()->user();
        $employee = $user->employee;

        if (!$employee) {
            return view('livewire.staff.dashboard', [
                'employee'         => null,
                'myLeaves'         => collect(),
                'balances'         => collect(),
                'pendingApprovals' => 0,
            ]);
        }

        $myLeaves = Leaves::where('employee_id', $employee->id)
            ->with('leaveType')
            ->latest()
            ->take(5)
            ->get();

        // ← Single clean query — year column exists on leave_balances table
        $balances = LeaveBalances::where('employee_id', $employee->id)
            ->where('year', now()->year)
            ->with('leaveType')
            ->get();

        $pendingApprovals = Leaves::whereHas('employee', fn($q) =>
                $q->where('line_manager_id', $employee->id)
            )
            ->where('approval_stage', 'pending_line_manager')
            ->count();

        return view('livewire.staff.dashboard',
            compact('employee', 'myLeaves', 'balances', 'pendingApprovals')
        );
    }
}
