<?php
// app/Livewire/CasualInterns/Edit.php

namespace App\Livewire\CasualInterns;

use App\Models\CasualIntern;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Edit extends Component
{
    public CasualIntern $casualIntern;

    public string $first_name   = '';
    public string $last_name    = '';
    public string $email        = '';
    public string $phone        = '';
    public string $national_id  = '';
    public string $type         = '';
    public string $division     = '';
    public string $branch       = '';
    public string $job_title    = '';
    public string $supervisor   = '';
    public string $start_date   = '';
    public string $end_date     = '';
    public string $daily_rate   = '';
    public string $institution  = '';
    public string $course       = '';
    public string $status       = '';
    public string $notes        = '';

    public function mount(CasualIntern $casualIntern): void
    {
        $this->casualIntern = $casualIntern;
        $this->first_name   = $casualIntern->first_name;
        $this->last_name    = $casualIntern->last_name;
        $this->email        = $casualIntern->email        ?? '';
        $this->phone        = $casualIntern->phone        ?? '';
        $this->national_id  = $casualIntern->national_id  ?? '';
        $this->type         = $casualIntern->type;
        $this->division     = $casualIntern->division;
        $this->branch       = $casualIntern->branch;
        $this->job_title    = $casualIntern->job_title    ?? '';
        $this->supervisor   = $casualIntern->supervisor   ?? '';
        $this->start_date   = $casualIntern->start_date->format('Y-m-d');
        $this->end_date     = $casualIntern->end_date?->format('Y-m-d') ?? '';
        $this->daily_rate   = $casualIntern->daily_rate   ?? '';
        $this->institution  = $casualIntern->institution  ?? '';
        $this->course       = $casualIntern->course       ?? '';
        $this->status       = $casualIntern->status;
        $this->notes        = $casualIntern->notes        ?? '';
    }

    protected function rules(): array
    {
        return [
            'first_name'  => 'required|string|max:100',
            'last_name'   => 'required|string|max:100',
            'email'       => 'nullable|email',
            'phone'       => 'nullable|string|max:20',
            'national_id' => 'nullable|string|max:20',
            'type'        => 'required|in:casual,intern',
            'division'    => 'required|in:eye,upper_primary,junior_school,administration,support',
            'branch'      => 'required|in:juja_road,kitisuru,south_c',
            'job_title'   => 'nullable|string|max:100',
            'supervisor'  => 'nullable|string|max:100',
            'start_date'  => 'required|date',
            'end_date'    => 'nullable|date|after:start_date',
            'daily_rate'  => 'nullable|numeric|min:0',
            'institution' => 'nullable|string|max:200',
            'course'      => 'nullable|string|max:200',
            'status'      => 'required|in:active,completed,terminated',
            'notes'       => 'nullable|string|max:1000',
        ];
    }

    public function save(): void
    {
        $this->validate();

        $this->casualIntern->update([
            'first_name'  => $this->first_name,
            'last_name'   => $this->last_name,
            'email'       => $this->email       ?: null,
            'phone'       => $this->phone       ?: null,
            'national_id' => $this->national_id ?: null,
            'type'        => $this->type,
            'division'    => $this->division,
            'branch'      => $this->branch,
            'job_title'   => $this->job_title   ?: null,
            'supervisor'  => $this->supervisor  ?: null,
            'start_date'  => $this->start_date,
            'end_date'    => $this->end_date    ?: null,
            'daily_rate'  => $this->daily_rate  ?: null,
            'institution' => $this->institution ?: null,
            'course'      => $this->course      ?: null,
            'status'      => $this->status,
            'notes'       => $this->notes       ?: null,
        ]);

        session()->flash('success', 'Record updated successfully.');
        $this->redirect(route('casual-interns.show', $this->casualIntern), navigate: true);
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.casual-interns.edit');
    }
}
