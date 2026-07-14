<?php

namespace App\Livewire\Leaves;

use App\Models\Leaves;
use App\Models\LeaveTypes;
use App\Services\LeaveService;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{

    use WithPagination;

    public string $search = '';

    public string $filterType = '';
    public string $filterStatus = '';

    public string $filterMonth = '';

    public bool $showRejectModal = false;

    public int $rejectId = 0;

    public string $rejectReason = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'filterType' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'filterMonth' => ['except' => ''],
    ];

    public function updatingSearch(): void {
        $this->resetPage();
    }

    public function approveLeave(int $id){
        // 
        $leave = Leaves::findOrFail($id);

        if($leave->status !== 'pending') return;

        $service = app(LeaveService::class);

        if (!$service->hasBalance($leave->employee_id, $leave->leave_type_id, $leave->days_requested, $leave->start_date->year)) {
            session()->flash('error', 'Insufficient leave balance for this employee.');
            return;
        }

        $service->approve($leave, auth()->id());
        session()->flash('success', 'Leave approved successfully.');

    }

    public function openRejectModal(int $id){
        $this->rejectId = $id;
        $this->rejectReason = '';
        $this->showRejectModal = true;
    }

    public function confirmReject(){
        $this->validate([
            'rejectReason' => 'required|string|max:255',
        ]);

        $leave = Leaves::findOrFail($this->rejectId);
        $service = app(LeaveService::class);
        $service->reject($leave, $this->rejectReason, auth()->id());
        session()->flash('success', 'Leave rejected successfully.');
        $this->showRejectModal = false;
    }

    public function cancelLeave(int $id){
        $leave = Leaves::findOrFail($id);

       app(LeaveService::class)->cancel($leave);

        session()->flash('success', 'Leave cancelled successfully.');
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $leaves = Leaves::with(['employee', 'leaveType', 'approvedBy'])
            ->whereHas('employee')
            ->when($this->search, fn($q) =>
                $q->whereHas('employee', fn($q) =>
                    $q->where('first_name', 'like', "%{$this->search}%")
                      ->orWhere('last_name',  'like', "%{$this->search}%")
                      ->orWhere('staff_number', 'like', "%{$this->search}%")
                )
            )
            ->when($this->filterType,   fn($q) => $q->where('leave_type_id', $this->filterType))
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterMonth,  fn($q) =>
                $q->whereMonth('start_date', substr($this->filterMonth, 5, 2))
                  ->whereYear('start_date',  substr($this->filterMonth, 0, 4))
            )
            ->latest()
            ->paginate(15);

            $leaveTypes = LeaveTypes::where('active', true)->get();
            $pendingCount = Leaves::pending()->count();
            
        return view('livewire.leaves.index', compact('leaves', 'leaveTypes', 'pendingCount'));
    }
}
