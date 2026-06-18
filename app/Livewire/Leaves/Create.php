<?php

namespace App\Livewire\Leaves;

use App\Models\Employees;
use App\Models\Leaves;
use App\Models\LeaveTypes;
use App\Services\LeaveService;
use Carbon\Carbon;
use livewire;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Create extends Component
{
    public int $employee_id = 0;

    public int $leave_type_id = 0;

    public string $start_date = '';

    public string $end_date = '';

    public string $reason = '';

    public int $days_requested = 0;

    protected function rules(): array
    {
        return [
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
            'leave_type_id' => ['required', 'integer', 'exists:leave_types,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'reason' => ['nullable', 'string'],
            'days_requested' => ['required', 'integer', 'min:1'],
        ];
    }

    public function updatedStartDate(): void {$this->calculateDays();}
    public function updatedEndDate(): void {$this->calculateDays();}

    public function calculateDays(){
        if($this->start_date && $this->end_date){
            $service = app(LeaveService::class);
            $this->days_requested = $service->countWorkingDays($this->start_date, $this->end_date);
        }
        
    }

    public function save(){
        $this->validate();

        $service = app(LeaveService::class);
        $year = Carbon::parse($this->start_date)->year;
        $days = $service->countWorkingDays($this->start_date, $this->end_date);
        $leaveType = LeaveTypes::find($this->leave_type_id);

        if($leaveType->requires_approval){
            $status = 'pending';
        }else{
            $status = 'approved';
        }

        if($status === 'approved' && !$service->hasBalance($this->employee_id, $this->leave_type_id, $year, $days)){
            $this->addError('days_requested', 'Not enough leave balance for this leave type.');
            return;
        }

        $leave = Leaves::create([
            'employee_id' => $this->employee_id,
            'leave_type_id' => $this->leave_type_id,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'reason' => $this->reason,
            'days_requested' => $days,
            'status' => $status,
        ]);

        if($status === 'approved'){
            $service->approve($leave, auth()->id());
        }

        session()->flash('message', 'Leave request submitted successfully.');
        $this->redirect(route('leaves.index'));

    }

    #[Layout('layouts.app')]
    public function render()
    {
        $employees = Employees::where('employment_status', 'active')->orderBy('first_name')->get();
        $leaveTypes = LeaveTypes::where('active', true)->get();
        return view('livewire.leaves.create', compact('employees', 'leaveTypes'));
    }

}

