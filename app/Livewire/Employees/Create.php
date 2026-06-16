<?php

namespace App\Livewire\Employees;

use Livewire\Attributes\Layout;
use Livewire\Component;

use App\Models\Employees;
use Flux\Flux;

 class Create extends Component
{
    public string $staff_number    = '';
    public string $first_name      = '';
    public string $last_name       = '';
    public string $email           = '';
    public string $phone           = '';
    public string $staff_type      = '';
    public string $division        = '';
    public string $job_title       = '';
    public string $date_of_joining = '';
    public string $gender          = '';
    public string $national_id     = '';
    public string $employment_status          = 'active';

    protected function rules(): array
    {
        return [
            'staff_number'    => 'required|string|unique:employees,staff_number',
            'first_name'      => 'required|string|max:100',
            'last_name'       => 'required|string|max:100',
            'email'           => 'nullable|email|unique:employees,email',
            'phone'           => 'nullable|string|max:20',
            'staff_type'      => 'required|in:teacher,admin,support_staff',
            'division'        => 'required|in:eye,upper_primary,junior_school,administration,support-services',
            'job_title'       => 'nullable|string|max:100',
            'date_of_joining' => 'required|date',
            'gender'          => 'nullable|in:male,female,other',
            'national_id'     => 'nullable|string|max:50',
            'employment_status'   => 'required|in:active,inactive,on_leave',
        ];
    }

    public function save(): void
    {
        $this->validate();

        Employees::create([
            'staff_number'    => $this->staff_number,
            'first_name'      => $this->first_name,
            'last_name'       => $this->last_name,
            'email'           => $this->email ?: null,
            'phone'           => $this->phone ?: null,
            'staff_type'      => $this->staff_type,
            'division'        => $this->division,
            'job_title'       => $this->job_title ?: null,
            'date_of_joining' => $this->date_of_joining,
            'gender'          => $this->gender ?: null,
            'national_id'     => $this->national_id ?: null,
            'employment_status'          => $this->employment_status ?: null,
        ]);

        // session()->flash('success', 'Employee created successfully.');

        Flux::toast(
            text: 'Employee created successfully.',
            variant: "success",
       );

        $this->redirect(route('employees.index'), navigate: true);
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.employees.create');
    }
}
