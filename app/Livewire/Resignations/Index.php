<?php


namespace App\Livewire\Resignations;

use App\Models\Resignation;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search       = '';
    public string $filterStatus = '';
    public string $filterMonth  = '';

    protected $queryString = [
        'search'       => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'filterMonth'  => ['except' => ''],
    ];

    public function updatingSearch(): void { $this->resetPage(); }

    #[Layout('layouts.app')]
    public function render()
    {
        $resignations = Resignation::with(['employee', 'recordedBy'])
            ->whereHas('employee')
            ->when($this->search, fn($q) =>
                $q->whereHas('employee', fn($q) =>
                    $q->where('first_name',    'like', "%{$this->search}%")
                      ->orWhere('last_name',   'like', "%{$this->search}%")
                      ->orWhere('staff_number','like', "%{$this->search}%")
                )
            )
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterMonth, fn($q) =>
                $q->whereMonth('resignation_date', substr($this->filterMonth, 5, 2))
                  ->whereYear('resignation_date',  substr($this->filterMonth, 0, 4))
            )
            ->latest()
            ->paginate(15);

        $activeCount = Resignation::active()->count();

        return view('livewire.resignations.index',
            compact('resignations', 'activeCount')
        );
    }
}