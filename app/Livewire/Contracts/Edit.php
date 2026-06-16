<?php

namespace App\Livewire\Contracts;

use App\Models\Contract;
use App\Models\Employees;
use Livewire\Component;

class Edit extends Component
{
    public Contract $contract;

    public int    $employee_id        = 0;
    public string $contract_type      = '';
    public string $start_date         = '';
    public string $end_date           = '';
    public int    $renewal_alert_days = 30;
    public string $status             = 'active';
    public string $notes              = '';

    public function mount(Contract $contract): void
    {
        $this->contract            = $contract;
        $this->employee_id         = $contract->employees_id;
        $this->contract_type       = $contract->contract_type;
        $this->start_date          = $contract->start_date->format('Y-m-d');
        $this->end_date            = $contract->end_date?->format('Y-m-d') ?? '';
        $this->renewal_alert_days  = $contract->renewal_alert_days;
        $this->status              = $contract->status;
        $this->notes               = $contract->notes ?? '';
    }

    protected function rules(): array
    {
        return [
            'employee_id'        => 'required|exists:employees,id',
            'contract_type'      => 'required|in:permanent,fixed-term,probation,part-time,internship',
            'start_date'         => 'required|date',
            'end_date'           => 'nullable|date|after:start_date',
            'renewal_alert_days' => 'required|integer|min:1|max:365',
            'status'             => 'required|in:active,expired,terminated,renewed',
            'notes'              => 'nullable|string|max:1000',
        ];
    }

    public function save(): void
    {
        $this->validate();

        $this->contract->update([
            'employee_id'        => $this->employee_id,
            'contract_type'      => $this->contract_type,
            'start_date'         => $this->start_date,
            'end_date'           => $this->end_date ?: null,
            'renewal_alert_days' => $this->renewal_alert_days,
            'status'             => $this->status,
            'notes'              => $this->notes ?: null,
        ]);

        session()->flash('success', 'Contract updated successfully.');
        $this->redirect(route('contracts.index'), navigate: true);
    }
    public function render()

    {
        $employees = Employees::orderBy('first_name')->get();
        return view('livewire.contracts.edit', compact('employees'));
    }
}
