<?php
// app/Livewire/Resignations/Report.php

namespace App\Livewire\Resignations;

use App\Models\Resignation;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Report extends Component
{
    public string $filterYear     = '';
    public string $filterBranch   = '';
    public string $filterDivision = '';

    public function mount(): void
    {
        $this->filterYear = now()->year;
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $query = Resignation::with('employee')
            ->whereHas('employee')
            ->when($this->filterYear, fn($q) =>
                $q->whereYear('resignation_date', $this->filterYear)
            )
            ->when($this->filterBranch, fn($q) =>
                $q->whereHas('employee', fn($q) =>
                    $q->where('branch', $this->filterBranch)
                )
            )
            ->when($this->filterDivision, fn($q) =>
                $q->whereHas('employee', fn($q) =>
                    $q->where('division', $this->filterDivision)
                )
            );

        $total       = $query->count();
        $completed   = (clone $query)->where('status', 'completed')->count();
        $inProgress  = (clone $query)->where('status', '!=', 'completed')->count();

        // By reason
        $byReason = (clone $query)->get()
            ->groupBy('reason')
            ->map(fn($rows) => $rows->count())
            ->sortDesc();

        // By month
        $byMonth = (clone $query)->get()
            ->groupBy(fn($r) => $r->resignation_date->format('M Y'))
            ->map(fn($rows) => $rows->count());

        // By division
        $byDivision = (clone $query)->get()
            ->groupBy(fn($r) => $r->employee->division_label)
            ->map(fn($rows) => $rows->count())
            ->sortDesc();

        // By branch
        $byBranch = (clone $query)->get()
            ->groupBy(fn($r) => $r->employee->branch_label)
            ->map(fn($rows) => $rows->count())
            ->sortDesc();

        $resignations = $query->latest()->get();

        return view('livewire.resignations.report', compact(
            'total', 'completed', 'inProgress',
            'byReason', 'byMonth', 'byDivision', 'byBranch',
            'resignations'
        ));
    }
}