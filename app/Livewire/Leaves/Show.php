<?php

namespace App\Livewire\Leaves;

use App\Models\LeaveBalances;
use App\Models\Leaves;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Show extends Component
{
    public Leaves $leave;

    public function mount(Leaves $leave)
    {
        $this->leave = $leave->load(['employee', 'leaveType', 'approvedBy']);
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $balance = LeaveBalances::where('employee_id', $this->leave->employee_id)
            ->where('leave_type_id', $this->leave->leave_type_id)
            ->where('year', $this->leave->start_date->year)
            ->first();
        return view('livewire.leaves.show', compact('balance'));
    }
}
