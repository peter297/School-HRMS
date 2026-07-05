<?php

namespace App\Livewire\Time;

use App\Models\Employees;
use App\Models\PermittedExits;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class Movements extends Component
{
    use WithPagination;

    public string $filterMonth = '';

    public string $filterBranch = '';

    public string $search = '';

    public bool $showAddModal = false;

    public int $employee_id = 0;

    public string $date = '';

    public string $exit_time = '13:00';

    public string $return_time = '';

    public string $reason = '';


    public function mount(): void
    {
        $this->filterMonth = now()->format('Y-m');
    }

    public function openAddModal():void{

        $this->reset(['employee_id', 'date', 'exit_time', 'return_time', 'reason']);
        $this->exit_time = '13:00';
        $this->showAddModal = true;
    }

    public function saveMovement(): void
    {
        $this->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'exit_time'    => 'required|date_format:H:i|after_or_equal:13:00|before_or_equal:14:00',
            'return_time'  => 'nullable|date_format:H:i|after:exit_time|before_or_equal:14:00',
            'reason'       => 'nullable|string|max:255',
        ]);

        PermittedExits::create([
            'employee_id'  => $this->employee_id,
            'date'         => $this->date,
            'exit_time'    => $this->exit_time,
            'return_time'  => $this->return_time ?: null,
            'reason'       => $this->reason ?: null,
            'recorded_by'  => auth()->id(),
        ]);

        $this->showAddModal = false;
        session()->flash('success', 'Permitted exit recorded successfully.');
    }

    #[Layout('layouts.app')]
    public function render()
    {
        [$year, $month] = $this->filterMonth
            ? explode('-', $this->filterMonth)
            : [now()->year, now()->month];

        $exits = PermittedExits::with(['employee', 'recordedBy'])
            ->whereMonth('date', $month)
            ->whereYear('date',  $year)
            ->whereHas('employee', fn($q) =>
                $q->when($this->search, fn($q) =>
                    $q->where('first_name',    'like', "%{$this->search}%")
                      ->orWhere('last_name',   'like', "%{$this->search}%")
                      ->orWhere('staff_number','like', "%{$this->search}%")
                )
                ->when($this->filterBranch, fn($q) => $q->where('branch', $this->filterBranch))
            )
            ->orderBy('date', 'desc')
            ->orderBy('exit_time')
            ->paginate(20);

        // Monthly summary per employee
        $summary = PermittedExits::with('employee')
            ->whereMonth('date', $month)
            ->whereYear('date',  $year)
            ->get()
            ->groupBy('employee_id')
            ->map(fn($rows) => [
                'employee'        => $rows->first()->employee,
                'total_movements' => $rows->count(),
                'avg_duration'    => $rows->avg(fn($r) => $r->duration_minutes),
                'days'            => $rows->pluck('date')->map->format('d')->unique()->sort()->values(),
            ])
            ->sortByDesc('total_movements')
            ->values();

        $employees = Employees::where('employment_status', 'active')->orderBy('first_name')->get();
        return view('livewire.time.movements', compact('exits', 'summary', 'employees'));
    }
}
