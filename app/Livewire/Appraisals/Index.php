<?php
// app/Livewire/Appraisals/Index.php

namespace App\Livewire\Appraisals;

use App\Models\Appraisal;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search      = '';
    public string $filterType  = '';
    public string $filterStatus= '';
    public string $filterYear  = '';

    public function mount(): void
    {
        $this->filterYear = now()->year;
    }

    public function updatingSearch(): void { $this->resetPage(); }

    public function render()
    {
        $appraisals = Appraisal::with(['employee', 'submittedBy', 'approvedBy'])
            ->whereHas('employee')
            ->when($this->search, fn($q) =>
                $q->whereHas('employee', fn($q) =>
                    $q->where('first_name',    'like', "%{$this->search}%")
                      ->orWhere('last_name',   'like', "%{$this->search}%")
                      ->orWhere('staff_number','like', "%{$this->search}%")
                )
            )
            ->when($this->filterType,   fn($q) => $q->where('type',   $this->filterType))
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterYear,   fn($q) => $q->where('year',   $this->filterYear))
            ->latest()
            ->paginate(15);

        $pendingCount = Appraisal::where('status', 'submitted')->count();

        return view('livewire.appraisals.index',
            compact('appraisals', 'pendingCount')
        )->layout('components.layouts.app');
    }
}