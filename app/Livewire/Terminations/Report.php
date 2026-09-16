<?php


namespace App\Livewire\Terminations;

use App\Models\Termination;
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
        $query = Termination::with('employee')
            ->whereHas('employee')
            ->when($this->filterYear, fn($q) =>
                $q->whereYear('termination_date', $this->filterYear)
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

        $total      = $query->count();
        $completed  = (clone $query)->where('status', 'completed')->count();
        $inProgress = (clone $query)->where('status', '!=', 'completed')->count();

        $byReason = (clone $query)->get()
            ->groupBy('reason')
            ->map(fn($rows) => $rows->count())
            ->sortDesc();

        $byMonth = (clone $query)->get()
            ->groupBy(fn($r) => $r->termination_date->format('M Y'))
            ->map(fn($rows) => $rows->count());

        $byDivision = (clone $query)->get()
            ->groupBy(fn($r) => $r->employee->division_label)
            ->map(fn($rows) => $rows->count())
            ->sortDesc();

        $byBranch = (clone $query)->get()
            ->groupBy(fn($r) => $r->employee->branch_label)
            ->map(fn($rows) => $rows->count())
            ->sortDesc();

        $terminations = $query->latest()->get();

        return view('livewire.terminations.report', compact(
            'total', 'completed', 'inProgress',
            'byReason', 'byMonth', 'byDivision', 'byBranch',
            'terminations'
        ));
    }
}