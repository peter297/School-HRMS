<?php

namespace App\Livewire\Employees;

use Livewire\Component;
use Livewire\WithPagination;

use Livewire\Attributes\Layout;


use App\Models\Employees;

class Index extends Component
{

    use WithPagination;

    public string $search = '';
    public string $filterType = '';
    public string $filterDivision = '';
    public string $filterStatus = '';
    public string $sortBy = 'first_name';
    public string $sortDirection = 'asc';

    public int $perPage = 10;
    protected $queryString = [

        'search' => ['except' => ''],
        'filterType' => ['except' => ''],
        'filterDivision' => ['except' => ''],
        'filterStatus' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sort(string $column):void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function deleteEmployee(int $id): void
    {
        $employee = Employees::findOrFail($id);
        $employee->delete();
        session()->flash('message', 'Employee ' . $employee->first_name . ' deleted successfully.');
    }


    #[Layout('layouts.app')]
    public function render()
    {

        $employees = Employees::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('first_name', 'like', '%' . $this->search . '%')
                        ->orWhere('last_name', 'like', '%' . $this->search . '%')
                        ->orWhere('staff_number', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterType, function ($query) {
                $query->where('staff_type', $this->filterType);
            })
            ->when($this->filterDivision, function ($query) {
                $query->where('division', $this->filterDivision);
            })
            ->when($this->filterStatus, function ($query) {
                $query->where('employment_status', $this->filterStatus);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
        return view('livewire.employees.index', compact('employees'));
    }
}
