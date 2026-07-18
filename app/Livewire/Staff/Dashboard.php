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

        // Guard — user has no linked employee record
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

        // ← Fix: use whereYear() not where('year')
        $balances = LeaveBalances::where('employee_id', $employee->id)
            ->whereYear('created_at', now()->year)  // ← only if you filter by year
            ->with('leaveType')
            ->get();

        // Better — the year column IS on leave_balances, so this is correct:
        $balances = LeaveBalances::where('employee_id', $employee->id)
            ->where('year', now()->year)  // ← this is fine on leave_balances
            ->with('leaveType')
            ->get();

        $pendingApprovals = Leaves::whereHas('employee', fn($q) =>
                $q->where('line_manager_id', $employee->id)
            )
            ->where('approval_stage', 'pending_line_manager')
            ->count();
    }
}
