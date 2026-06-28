<?php

namespace App\Livewire\Employees;

use App\Exports\EmployeesExport;
use App\Exports\EmployeesImportTemplate;
use App\Imports\EmployeesImport;
use App\Models\Employees;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class Index extends Component
{

    use WithPagination, WithFileUploads;

    public string $search = '';
    public string $filterType = '';
    public string $filterDivision = '';
    public string $filterStatus = '';

    public string $filterBranch = '';
    public string $sortBy = 'first_name';
    public string $sortDirection = 'asc';

    public array $selectedIds = [];

    public bool $selectAll = false;

    public bool $showImportModal = false;

    public  $importFile  = null;

    public array $importErrors = [];

    public ?int $importedCount = null;


    public int $perPage = 10;
    protected $queryString = [

        'search' => ['except' => ''],
        'filterType' => ['except' => ''],
        'filterDivision' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'filterBranch' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterType(): void{
        $this->resetPage();
    }

    public function updatingFilterBranch(): void{
        $this->resetPage();
    }

    public function updatedSelectAll(bool $value): void{
        if($value){
            $this->selectedIds = $this->getFilteredQuery()
                ->pluck('id')
                ->map(fn($id) => (string) $id)
                ->toArray();
        }else{
            $this->selectedIds = [];
        }
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
        $this->selectedIds = array_diff($this->selectedIds, [(string) $id]);
        session()->flash('message', 'Employee ' . $employee->first_name . ' deleted successfully.');
    }

    // Import Employee Excel file

    public function openImportModal(): void{
        $this->importFile = null;
        $this->importErrors = [];
        $this->importedCount = null;
        $this->showImportModal = true;
    }

    public function downloadTemplate(): BinaryFileResponse{
        return Excel::download(new EmployeesImportTemplate(), 'employee_import_template.xlsx');
    }

    public function importEmployees(): void{
        $this->validate([
            'importFile' => 'required|file|mimes:xlsx,xls|max:5120',
        ]);

        $this->importErrors = [];

        try {
            $import = new EmployeesImport();
            Excel::import($import, $this->importFile->getRealPath());

            $errors = $import->errors();

            if ($errors->count() > 0) {
                foreach ($errors as $error) {
                    $this->importErrors[] = $error->getMessage();
                }
            }

            session()->flash('success', 'Import completed. ' . ($errors->count() > 0 ? count($this->importErrors) . ' row(s) skipped.' : 'All rows imported.'));
            $this->showImportModal = false;

        } catch (\Exception $e) {
            $this->importErrors[] = 'Import failed: ' . $e->getMessage();
        }
    }

    public function exportSelected(): BinaryFileResponse{
        $ids = !empty($this->selectedIds)
            ? array_map('intval', $this->selectedIds)
            : null;

        return Excel::download(
            new EmployeesExport(
                ids: $ids,
                search: $this->search,
                filterType: $this->filterType,
                filterDivision: $this->filterDivision,
                filterStatus: $this->filterStatus,
                filterBranch: $this->filterBranch,
            ),
            'employees_' . now()->format('d_m_Y') . '.xlsx'
        );
    }

    public function getFilteredQuery(){
        return Employees::query()
            ->when($this->search, fn($q) =>
                $q->where(fn($q) =>
                    $q->where('first_name',    'like', "%{$this->search}%")
                      ->orWhere('last_name',   'like', "%{$this->search}%")
                      ->orWhere('staff_number','like', "%{$this->search}%")
                      ->orWhere('email',       'like', "%{$this->search}%")
                )
            )
            ->when($this->filterType,     fn($q) => $q->where('staff_type', $this->filterType))
            ->when($this->filterDivision, fn($q) => $q->where('division',   $this->filterDivision))
            ->when($this->filterStatus,   fn($q) => $q->where('status',     $this->filterStatus))
            ->when($this->filterBranch,   fn($q) => $q->where('branch',     $this->filterBranch));
    }


    #[Layout('layouts.app')]
    public function render()
    {

        $employees = $this->getFilteredQuery()
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
        return view('livewire.employees.index', compact('employees'));
    }
}
