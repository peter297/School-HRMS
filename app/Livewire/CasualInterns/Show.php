<?php


namespace App\Livewire\CasualInterns;

use App\Models\CasualIntern;
use App\Models\Employees;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Show extends Component
{
    public CasualIntern $casualIntern;

    // Promote modal
    public bool   $showPromoteModal  = false;
    public string $promoteStaffType  = '';
    public string $promoteStaffNumber = '';
    public string $promoteJobTitle   = '';
    public string $promoteDateOfJoining = '';

    // Status update
    public bool   $showStatusModal  = false;
    public string $newStatus        = '';

    public function mount(CasualIntern $casualIntern): void
    {
        $this->casualIntern = $casualIntern->load(['promotedToEmployee', 'recordedBy']);
    }

    public function openPromoteModal(): void
    {
        $this->promoteStaffNumber    = '';
        $this->promoteStaffType      = 'admin';
        $this->promoteJobTitle       = $this->casualIntern->job_title ?? '';
        $this->promoteDateOfJoining  = today()->format('Y-m-d');
        $this->showPromoteModal      = true;
    }

    public function confirmPromote(): void
    {
        $this->validate([
            'promoteStaffNumber'   => 'required|string|unique:employees,staff_number',
            'promoteStaffType'     => 'required|in:teacher_eye,teacher_upper_primary,teacher_junior,admin,support_staff',
            'promoteJobTitle'      => 'nullable|string|max:100',
            'promoteDateOfJoining' => 'required|date',
        ]);

        // Create new employee record
        $employee = Employees::create([
            'staff_number'     => $this->promoteStaffNumber,
            'first_name'       => $this->casualIntern->first_name,
            'last_name'        => $this->casualIntern->last_name,
            'email'            => $this->casualIntern->email,
            'phone'            => $this->casualIntern->phone,
            'national_id'      => $this->casualIntern->national_id,
            'staff_type'       => $this->promoteStaffType,
            'division'         => $this->casualIntern->division,
            'branch'           => $this->casualIntern->branch,
            'job_title'        => $this->promoteJobTitle ?: null,
            'date_of_joining'  => $this->promoteDateOfJoining,
            'employment_status'=> 'active',
        ]);

        // Mark casual/intern as promoted
        $this->casualIntern->update([
            'promoted'                => true,
            'promoted_date'           => today()->toDateString(),
            'promoted_to_employee_id' => $employee->id,
            'status'                  => 'completed',
        ]);

        $this->showPromoteModal = false;
        $this->casualIntern->refresh();
        session()->flash('success', "{$this->casualIntern->full_name} has been promoted to a full employee. Staff number: {$employee->staff_number}");
    }

    public function updateStatus(string $status): void
    {
        $this->casualIntern->update(['status' => $status]);
        $this->casualIntern->refresh();
        session()->flash('success', 'Status updated successfully.');
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.casual-interns.show');
    }
}
