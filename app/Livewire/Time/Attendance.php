<?php

namespace App\Livewire\Time;

use App\Models\AttendanceRecords;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class Attendance extends Component
{

use WithPagination;

public string $search = '';

public string $filterStatus = '';

public string $filterDate = '';

public string $filterMonth = '';

public string $filterBranch = '';

public function updatingSearch(): void
{
    $this->resetPage();
}


    #[Layout('layouts.app')]
    public function render()
    {
       $records = AttendanceRecords::with(['employee', 'attendanceLog'])
            ->whereHas('employee', fn($q) =>
                $q->when($this->search, fn($q) =>
                    $q->where('first_name',    'like', "%{$this->search}%")
                      ->orWhere('last_name',   'like', "%{$this->search}%")
                      ->orWhere('staff_number','like', "%{$this->search}%")
                )
                ->when($this->filterBranch, fn($q) =>
                    $q->where('branch', $this->filterBranch)
                )
            )
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterDate,   fn($q) => $q->whereDate('date', $this->filterDate))
            ->when($this->filterMonth,  fn($q) =>
                $q->whereMonth('date', substr($this->filterMonth, 5, 2))
                  ->whereYear('date',  substr($this->filterMonth, 0, 4))
            )
            ->latest('date')
            ->paginate(20);

            // dd($records);
        return view('livewire.time.attendance', compact('records'));
    }
}
