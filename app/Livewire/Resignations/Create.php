<?php


namespace App\Livewire\Resignations;

use App\Models\Employees;
use App\Models\Resignation;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Create extends Component
{
    public int    $employee_id      = 0;
    public string $resignation_date = '';
    public string $notice_period    = '1_month';
    public string $notice_end_date  = '';
    public string $last_working_day = '';
    public string $reason           = '';
    public string $reason_details   = '';
    public string $notes            = '';

    protected function rules(): array
    {
        return [
            'employee_id'      => 'required|exists:employees,id',
            'resignation_date' => 'required|date',
            'notice_period'    => 'required|in:1_week,2_weeks,1_month,2_months,3_months',
            'notice_end_date'  => 'required|date|after:resignation_date',
            'last_working_day' => 'nullable|date|after_or_equal:resignation_date',
            'reason'           => 'required|in:personal,better_opportunity,relocation,health,further_studies,retirement,other',
            'reason_details'   => 'nullable|string|max:1000',
            'notes'            => 'nullable|string|max:1000',
        ];
    }

    public function updatedResignationDate(): void
    {
        $this->calculateNoticeEndDate();
    }

    public function updatedNoticePeriod(): void
    {
        $this->calculateNoticeEndDate();
    }

    public function calculateNoticeEndDate(): void
    {
        if (!$this->resignation_date || !$this->notice_period) return;

        $date = \Carbon\Carbon::parse($this->resignation_date);

        $this->notice_end_date = match($this->notice_period) {
            '1_week'   => $date->addWeek()->format('Y-m-d'),
            '2_weeks'  => $date->addWeeks(2)->format('Y-m-d'),
            '1_month'  => $date->addMonth()->format('Y-m-d'),
            '2_months' => $date->addMonths(2)->format('Y-m-d'),
            '3_months' => $date->addMonths(3)->format('Y-m-d'),
            default    => '',
        };
    }

    public function save(): void
    {
        $this->validate();

        Resignation::create([
            'employee_id'      => $this->employee_id,
            'resignation_date' => $this->resignation_date,
            'notice_period'    => $this->notice_period,
            'notice_end_date'  => $this->notice_end_date,
            'last_working_day' => $this->last_working_day ?: null,
            'reason'           => $this->reason,
            'reason_details'   => $this->reason_details ?: null,
            'notes'            => $this->notes ?: null,
            'status'           => 'notice_served',
            'notice_served'    => true,
            'notice_served_date' => now()->toDateString(),
            'recorded_by'      => auth()->id(),
        ]);

        // Update employee status
        \Illuminate\Support\Facades\DB::table('employees')
            ->where('id', $this->employee_id)
            ->update(['employment_status' => 'inactive']);

        session()->flash('success', 'Resignation recorded successfully.');
        $this->redirect(route('resignations.index'), navigate: true);
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $employees = Employees::where('employment_status', 'active')
            ->orderBy('first_name')
            ->get();

        return view('livewire.resignations.create', compact('employees'));
    }
}
