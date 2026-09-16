<?php


namespace App\Livewire\Terminations;

use App\Models\Employees;
use App\Models\Termination;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Create extends Component
{
    public int    $employee_id       = 0;
    public string $termination_date  = '';
    public string $notice_date       = '';
    public string $last_working_day  = '';
    public string $reason            = '';
    public string $reason_details    = '';
    public string $hr_notes          = '';

    protected function rules(): array
    {
        return [
            'employee_id'      => 'required|exists:employees,id',
            'termination_date' => 'required|date',
            'notice_date'      => 'nullable|date',
            'last_working_day' => 'nullable|date|after_or_equal:termination_date',
            'reason'           => 'required|in:misconduct,redundancy,contract_end,performance,absenteeism,other',
            'reason_details'   => 'nullable|string|max:2000',
            'hr_notes'         => 'nullable|string|max:1000',
        ];
    }

    public function save(): void
    {
        $this->validate();

        Termination::create([
            'employee_id'      => $this->employee_id,
            'termination_date' => $this->termination_date,
            'notice_date'      => $this->notice_date ?: null,
            'last_working_day' => $this->last_working_day ?: null,
            'reason'           => $this->reason,
            'reason_details'   => $this->reason_details ?: null,
            'hr_notes'         => $this->hr_notes ?: null,
            'status'           => 'initiated',
            'recorded_by'      => auth()->id(),
        ]);

        // Set employee status to inactive
        DB::table('employees')
            ->where('id', $this->employee_id)
            ->update(['employment_status' => 'inactive']);

        session()->flash('success', 'Termination recorded successfully.');
        $this->redirect(route('terminations.index'), navigate: true);
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $employees = Employees::where('employment_status', 'active')
            ->orderBy('first_name')
            ->get();

        return view('livewire.terminations.create', compact('employees'));
    }
}