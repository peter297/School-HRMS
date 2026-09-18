<?php

namespace App\Livewire\CasualInterns;

use App\Models\CasualIntern;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search         = '';
    public string $filterType     = '';
    public string $filterStatus   = '';
    public string $filterBranch   = '';
    public string $filterDivision = '';

    protected $queryString = [
        'search'         => ['except' => ''],
        'filterType'     => ['except' => ''],
        'filterStatus'   => ['except' => ''],
        'filterBranch'   => ['except' => ''],
        'filterDivision' => ['except' => ''],
    ];

    public function updatingSearch(): void { $this->resetPage(); }

    #[Layout('layouts.app')]
    public function render()
    {
        $records = CasualIntern::query()
            ->when($this->search, fn($q) =>
                $q->where(fn($q) =>
                    $q->where('first_name',   'like', "%{$this->search}%")
                      ->orWhere('last_name',  'like', "%{$this->search}%")
                      ->orWhere('staff_number','like',"%{$this->search}%")
                      ->orWhere('email',      'like', "%{$this->search}%")
                )
            )
            ->when($this->filterType,     fn($q) => $q->where('type',     $this->filterType))
            ->when($this->filterStatus,   fn($q) => $q->where('status',   $this->filterStatus))
            ->when($this->filterBranch,   fn($q) => $q->where('branch',   $this->filterBranch))
            ->when($this->filterDivision, fn($q) => $q->where('division', $this->filterDivision))
            ->orderBy('first_name')
            ->paginate(15);

        $totalCasuals = CasualIntern::casuals()->active()->count();
        $totalInterns = CasualIntern::interns()->active()->count();

        return view('livewire.casual-interns.index',
            compact('records', 'totalCasuals', 'totalInterns')
        );
    }
}
