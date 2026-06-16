<?php

namespace App\Livewire\Contracts;

use App\Models\Contract;
use App\Models\Employees;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Create extends Component
{
    public int $employee_id =  0;
    public string $contract_type = '';
    public string $start_date = '';
    public ?string $end_date = null;
    public int $renewal_alert_days = 30;
    public string $status = 'active';
    public ?string $notes = null;

    public function rules(): array
    {
        return [
            'employee_id' => 'required|exists:employees,id',
            'contract_type' => 'required|in:permanent,fixed-term,probation,part-time,internship',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'renewal_alert_days' => 'required|integer|min:1|max:365',
            'status' => 'required|in:active,expired,terminated,renewed',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function save()
    {
        $this->validate();

        // Logic to save the contract
        Contract::create([
            'employees_id' => $this->employee_id,
            'contract_type' => $this->contract_type,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date ? : null,
            'renewal_alert_days' => $this->renewal_alert_days,
            'status' => $this->status,
            'notes' => $this->notes ? : null,
            // 'created_by' => auth()->id(),
        ]);

        session()->flash('message', 'Contract created successfully.');
        return redirect(route('contracts.index'));
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $employees = Employees::orderBy('first_name')->get();
        return view('livewire.contracts.create', compact('employees'));
    }
}
