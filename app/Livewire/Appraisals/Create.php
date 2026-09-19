<?php
// app/Livewire/Appraisals/Create.php

namespace App\Livewire\Appraisals;

use App\Models\Appraisal;
use App\Models\AppraisalKpi;
use App\Models\Employees;
use App\Services\AppraisalKpiService;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Create extends Component
{
    public int    $employee_id = 0;
    public string $type        = 'annual';
    public string $period      = '';
    public int    $year        = 0;

    public function mount(): void
    {
        $this->year   = now()->year;
        $this->period = now()->year . ' — Annual';
    }

    public function updatedType(): void
    {
        $this->period = match($this->type) {
            'annual'    => $this->year . ' — Annual',
            'mid_year'  => $this->year . ' — Mid-year',
            'probation' => $this->year . ' — Probation',
            default     => $this->year . ' — Appraisal',
        };
    }

    protected function rules(): array
    {
        return [
            'employee_id' => 'required|exists:employees,id',
            'type'        => 'required|in:annual,mid_year,probation',
            'period'      => 'required|string|max:100',
            'year'        => 'required|integer|min:2020|max:2100',
        ];
    }

    public function save(): void
    {
        $this->validate();

        $employee = Employees::findOrFail($this->employee_id);
        $service  = app(AppraisalKpiService::class);
        $kpis     = $service->getKpisForStaffType($employee->staff_type);

        $appraisal = Appraisal::create([
            'employee_id' => $this->employee_id,
            'type'        => $this->type,
            'period'      => $this->period,
            'year'        => $this->year,
            'status'      => 'pending',
        ]);

        foreach ($kpis as $kpi) {
            AppraisalKpi::create([
                'appraisal_id' => $appraisal->id,
                'kpi_key'      => $kpi['key'],
                'kpi_label'    => $kpi['label'],
                'sort_order'   => $kpi['order'],
            ]);
        }

        session()->flash('success', 'Appraisal created. The line manager can now fill it in.');
        $this->redirect(route('appraisals.show', $appraisal), navigate: true);
    }


    #[Layout('layouts.app')]
    public function render()
    {
        $employees = Employees::where('employment_status', 'active')
            ->orderBy('first_name')
            ->get();

        return view('livewire.appraisals.create', compact('employees'));
    }
}