<?php

namespace App\Livewire\Staff\Approvals;

use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $filterStage = 'pending_line_manager';

    // Approve Modal

    public bool $showApproveModal = false;
    public int $approvingId = 0;
    public string $approveNotes = '';
    public string $taskAssignedTo = '';
    public string $taskDescription = '';

    // Reject Modal

    public bool $showRejectModal = false;
    public int $rejectingId = 0;
    public string $rejectNotes = '';

    public function openApproveModal(int $id): void{
        $this->approvingId = $id;
        $this->approveNotes = '';
        $this->taskAssignedTo = '';
        $this->taskDescription = '';
        $this->showApproveModal = true;
    }

    public function openRejectModal(int $id): void{
        $this->rejectingId = $id;
        $this->rejectNotes = '';
        $this->showRejectModal = true;
    }

    public function confirmApprove(): void{
        $this->validate([
            
        ]);
    }




    public function render()
    {
        return view('livewire.staff.approvals.index');
    }
}
