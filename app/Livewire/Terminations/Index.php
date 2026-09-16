<?php
// app/Livewire/Terminations/Index.php

namespace App\Livewire\Terminations;

use App\Models\Termination;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search        = '';
    public string $filterReason  = '';
    public string $filterStatus  = '';
    public string $filterMonth   = '';

    protected $queryString = [
        'search'       => ['except' => ''],
        'filterReason' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'filterMonth'  => ['except' => ''],
    ];

    public function updatingSearch(): void { $this->resetPage(); }

    #[Layout('layouts.app')]
    public function render()
    {
        $terminations = Termination::with(['employee', 'recordedBy'])
            ->whereHas('employee')
            ->when($this->search, fn($q) =>
                $q->whereHas('employee', fn($q) =>
                    $q->where('first_name',    'like', "%{$this->search}%")
                      ->orWhere('last_name',   'like', "%{$this->search}%")
                      ->orWhere('staff_number','like', "%{$this->search}%")
                )
            )
            ->when($this->filterReason, fn($q) => $q->where('reason', $this->filterReason))
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterMonth, fn($q) =>
                $q->whereMonth('termination_date', substr($this->filterMonth, 5, 2))
                  ->whereYear('termination_date',  substr($this->filterMonth, 0, 4))
            )
            ->latest()
            ->paginate(15);

        $activeCount = Termination::active()->count();

        return view('livewire.terminations.index',
            compact('terminations', 'activeCount')
        );
    }
}
