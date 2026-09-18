<?php


namespace App\Livewire\Disciplinary;

use App\Models\DisciplinaryCase;
use App\Models\Employees;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Create extends Component
{
    public int    $employee_id   = 0;
    public string $category      = '';
    public string $description   = '';
    public string $incident_date = '';

    protected function rules(): array
    {
        return [
            'employee_id'   => 'required|exists:employees,id',
            'category'      => 'required|in:misconduct,insubordination,absenteeism,performance,harassment,theft,other',
            'description'   => 'required|string|min:20',
            'incident_date' => 'required|date',
        ];
    }

    public function save(): void
    {
        $this->validate();

        $case = DisciplinaryCase::create([
            'case_number'   => DisciplinaryCase::generateCaseNumber(),
            'employee_id'   => $this->employee_id,
            'category'      => $this->category,
            'description'   => $this->description,
            'incident_date' => $this->incident_date,
            'stage'         => 'show_cause_issued',
            'outcome'       => 'pending',
            'recorded_by'   => auth()->id(),
        ]);

        session()->flash('success', "Disciplinary case {$case->case_number} opened successfully.");
        $this->redirect(route('disciplinary.show', $case), navigate: true);
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $employees = Employees::where('employment_status', 'active')
            ->orderBy('first_name')
            ->get();

        return view('livewire.disciplinary.create', compact('employees'));
    }
}