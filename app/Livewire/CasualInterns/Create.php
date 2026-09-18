<?php
// app/Livewire/CasualInterns/Create.php

namespace App\Livewire\CasualInterns;

use App\Models\CasualIntern;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Create extends Component
{
    public string $staff_number = '';
    public string $first_name   = '';
    public string $last_name    = '';
    public string $email        = '';
    public string $phone        = '';
    public string $national_id  = '';
    public string $type         = 'casual';
    public string $division     = '';
    public string $branch       = '';
    public string $job_title    = '';
    public string $supervisor   = '';
    public string $start_date   = '';
    public string $end_date     = '';
    public string $daily_rate   = '';
    public string $institution  = '';
    public string $course       = '';
    public string $notes        = '';

    protected function rules(): array
    {
        return [
            'staff_number' => 'required|string|unique:casual_interns,staff_number',
            'first_name'   => 'required|string|max:100',
            'last_name'    => 'required|string|max:100',
            'email'        => 'nullable|email',
            'phone'        => 'nullable|string|max:20',
            'national_id'  => 'nullable|string|max:20',
            'type'         => 'required|in:casual,intern',
            'division'     => 'required|in:eye,upper_primary,junior_school,administration,support',
            'branch'       => 'required|in:juja_road,kitisuru,south_c',
            'job_title'    => 'nullable|string|max:100',
            'supervisor'   => 'nullable|string|max:100',
            'start_date'   => 'required|date',
            'end_date'     => 'nullable|date|after:start_date',
            'daily_rate'   => 'nullable|numeric|min:0',
            'institution'  => 'nullable|string|max:200',
            'course'       => 'nullable|string|max:200',
            'notes'        => 'nullable|string|max:1000',
        ];
    }

    public function save(): void
    {
        $this->validate();

        CasualIntern::create([
            'staff_number' => $this->staff_number,
            'first_name'   => $this->first_name,
            'last_name'    => $this->last_name,
            'email'        => $this->email    ?: null,
            'phone'        => $this->phone    ?: null,
            'national_id'  => $this->national_id ?: null,
            'type'         => $this->type,
            'division'     => $this->division,
            'branch'       => $this->branch,
            'job_title'    => $this->job_title   ?: null,
            'supervisor'   => $this->supervisor  ?: null,
            'start_date'   => $this->start_date,
            'end_date'     => $this->end_date    ?: null,
            'daily_rate'   => $this->daily_rate  ?: null,
            'institution'  => $this->institution ?: null,
            'course'       => $this->course      ?: null,
            'notes'        => $this->notes       ?: null,
            'status'       => 'active',
            'recorded_by'  => auth()->id(),
        ]);

        session()->flash('success', ucfirst($this->type) . ' record created successfully.');
        $this->redirect(route('casual-interns.index'), navigate: true);
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.casual-interns.create');
    }
}
