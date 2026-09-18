<?php

namespace App\Livewire\Disciplinary;

use App\Models\DisciplinaryCase;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search         = '';
    public string $filterCategory = '';
    public string $filterStage    = '';
    public string $filterOutcome  = '';

    protected $queryString = [
        'search'         => ['except' => ''],
        'filterCategory' => ['except' => ''],
        'filterStage'    => ['except' => ''],
        'filterOutcome'  => ['except' => ''],
    ];

    public function updatingSearch(): void { $this->resetPage(); }

    #[Layout('layouts.app')]
    public function render()
    {
        $cases = DisciplinaryCase::with(['employee', 'recordedBy'])
            ->whereHas('employee')
            ->when($this->search, fn($q) =>
                $q->where('case_number', 'like', "%{$this->search}%")
                  ->orWhereHas('employee', fn($q) =>
                      $q->where('first_name',    'like', "%{$this->search}%")
                        ->orWhere('last_name',   'like', "%{$this->search}%")
                        ->orWhere('staff_number','like', "%{$this->search}%")
                  )
            )
            ->when($this->filterCategory, fn($q) => $q->where('category', $this->filterCategory))
            ->when($this->filterStage,    fn($q) => $q->where('stage',    $this->filterStage))
            ->when($this->filterOutcome,  fn($q) => $q->where('outcome',  $this->filterOutcome))
            ->latest()
            ->paginate(15);

        $openCount = DisciplinaryCase::whereNotIn('stage', ['closed'])->count();

        return view('livewire.disciplinary.index',
            compact('cases', 'openCount')
        );
    }
}