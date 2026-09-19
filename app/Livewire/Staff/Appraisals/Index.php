<?php


namespace App\Livewire\Staff\Appraisals;

use App\Models\Appraisal;
use App\Models\Employees;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Layout('components.layouts.staff')]
    public function render()
    {
        $employee = auth()->user()->employee;

        // Appraisals for direct reports assigned to this manager
        $appraisals = Appraisal::with('employee')
            ->whereHas('employee', fn($q) =>
                $q->where('line_manager_id', $employee?->id)
            )
            ->latest()
            ->paginate(10);

        // Own appraisals
        $myAppraisals = Appraisal::where('employee_id', $employee?->id)
            ->latest()
            ->get();

        return view('livewire.staff.appraisals.index',
            compact('appraisals', 'myAppraisals')
        );
    }
}
