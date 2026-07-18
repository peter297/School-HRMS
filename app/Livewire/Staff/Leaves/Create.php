<?php

namespace App\Livewire\Staff\Leaves;

use App\Models\Leaves;
use App\Models\LeaveTypes;
use App\Services\LeaveService;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Create extends Component
{
    public int $leave_type_id = 0;

    public string $start_date = '';

    public string $end_date = '';

    public string $reason = '';

    public int $days_requested = 0;

    protected function rules(): array{
        return [
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string|max:500',
        ];
    }

    public function updateStartDate(): void {$this->calculateDays();}
    public function updateEndDate(): void{$this->calculateDays();}

    public function calculateDays(): void{
        if($this->start_date && $this->end_date){
            $this->days_requested = app(LeaveService::class)
            ->countWorkingDays($this->start_date, $this->end_date);
        }
    }

    public function save(): void{
        $this->validate();

        $employee = auth()->user()->employee;

        if(!$employee){
            $this->addError('leave_type_id', 'Your Account is not linked to an Employee Record. Contact the HR Office');
            return;
        }

        $service = app(LeaveService::class);
        $days = $service->countWorkingDays($this->start_date, $this->end_date);
        $year = Carbon::parse($this->start_date)->year;

        if(!$service->hasBalance($employee->id, $this->leave_type_id, $days, $year)){
            $this->addError('days_requested', 'You do not have enough leave balance for this request');
            return;
        }

        Leaves::create([
            'employee_id' => $employee->id,
            'leave_type_id' => $this->leave_type_id,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'days_requested' => $days,
            'reason' => $this->reason ?: null,
            'status' => 'pending',
            'approval_stage' => 'pending_line_manager',


        ]);

        session()->flash('success', 'Leave application submitted, Awaiting your line manager\'s approval.');
        $this->redirect(route('staff.leaves.index'), navigate:true);


    }
    #[Layout('components.layouts.staff')]
    public function render()
    {
        $leaveTypes = LeaveTypes::where('active', true)->get();
        return view('livewire.staff.leaves.create', compact('leaveTypes'));
    }
}
