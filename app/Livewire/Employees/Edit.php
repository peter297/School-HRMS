<?php
// app/Livewire/Employees/Edit.php

namespace App\Livewire\Employees;

use App\Models\Employees;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Edit extends Component
{
    public Employees $employee;

    public string $staff_number = '';
    public string $first_name   = '';
    public string $last_name    = '';
    public string $email        = '';
    public string $phone        = '';
    public string $staff_type   = '';
    public string $division     = '';
    public string $job_title    = '';

    public string $branch            = '';
    public string $date_of_joining   = '';
    public string $gender            = '';
    public string $national_id       = '';
    public string $employment_status = 'active';

    public int $line_manager_id = 0;

    public function mount(Employees $employee): void
    {
        $this->employee          = $employee;
        $this->staff_number      = $employee->staff_number;
        $this->first_name        = $employee->first_name;
        $this->last_name         = $employee->last_name;
        $this->email             = $employee->email ?? '';
        $this->phone             = $employee->phone ?? '';
        $this->staff_type        = $employee->staff_type;
        $this->division          = $employee->division;
        $this->branch            = $employee->branch;
        $this->job_title         = $employee->job_title ?? '';
        $this->date_of_joining   = $employee->date_of_joining->format('Y-m-d');
        $this->gender            = $employee->gender ?? '';
        $this->national_id       = $employee->national_id ?? '';
        $this->employment_status = $employee->employment_status;
        $this->line_manager_id = $employee->line_manager_id ?? 0;
    }

    protected function rules(): array
    {
        return [
            'staff_number' => "required|string|unique:employees,staff_number,{$this->employee->id}",
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'email'      => "nullable|email|unique:employees,email,{$this->employee->id}",
            'phone'             => 'nullable|string|max:20',
            'staff_type'        => 'required|in:teacher,admin,support_staff',
            'division'          => 'required|in:eye,upper_primary,junior_school,administration,support_services',
            'job_title'         => 'nullable|string|max:100',
            'date_of_joining'   => 'required|date',
            'gender'            => 'nullable|in:male,female,other',
            'national_id'       => 'nullable|string|max:50',
            'employment_status' => 'required|in:active,inactive,on_leave',
        ];
    }

    public function save(): void
    {
        $this->validate();

        $this->employee->update([
            'staff_number'      => $this->staff_number,
            'first_name'        => $this->first_name,
            'last_name'         => $this->last_name,
            'email'             => $this->email ?: null,
            'phone'             => $this->phone ?: null,
            'staff_type'        => $this->staff_type,
            'division'          => $this->division,
            'job_title'         => $this->job_title ?: null,
            'date_of_joining'   => $this->date_of_joining,
            'gender'            => $this->gender ?: null,
            'national_id'       => $this->national_id ?: null,
            'employment_status' => $this->employment_status,
        ]);

        session()->flash('success', 'Employee updated successfully.');
        $this->redirect(route('employees.index'), navigate: true);
    }

    #[Layout('layouts.app')]
    public function render()
    {

        return view('livewire.employees.edit');
    }
}
