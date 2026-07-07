<?php

namespace App\Livewire\Contracts;

use App\Models\Contract;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{

    use WithPagination;

    public string $search = '';
    public string $filterType = '';
    public string $filterStatus = '';
    public bool $showExpiring = false;

     protected $queryString = [
        'search' => ['except' => ''],
        'filterType' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'showExpiring' => ['except' => false],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function deleteContract($id){

        Contract::findOrFail($id)->delete();
        session()->flash('message', 'Contract deleted successfully.');
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $query = Contract::with('employee', 'createdBy')
            ->whereHas('employee')
            ->when($this->search, fn($q) =>
                $q->whereHas('employee', fn($q) =>
                    $q->where('first_name', 'like', "%{$this->search}%")
                      ->orWhere('last_name',  'like', "%{$this->search}%")
                      ->orWhere('staff_number', 'like', "%{$this->search}%")
                )
            )
            ->when($this->filterType,   fn($q) => $q->where('contract_type', $this->filterType))
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->showExpiring,  fn($q) => $q->expiringSoon())
            ->latest('start_date');

        $contracts     = $query->paginate(15);
        $expiringCount = Contract::expiringSoon()->count();

        return view('livewire.contracts.index', compact('contracts', 'expiringCount'));
    }
}
