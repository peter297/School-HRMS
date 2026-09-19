<?php


namespace App\Livewire\Appraisals;

use App\Models\Appraisal;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Component;

class Show extends Component
{
    public Appraisal $appraisal;

    public bool   $showApproveModal = false;
    public bool   $showRejectModal  = false;
    public string $hrComments       = '';
    public string $rejectionReason  = '';

    public function mount(Appraisal $appraisal): void
    {
        $this->appraisal = $appraisal->load([
            'employee', 'kpis', 'submittedBy', 'approvedBy',
        ]);
    }

    public function approve(): void
    {
        $this->validate(['hrComments' => 'nullable|string|max:1000']);

        $result = $this->appraisal->calculateOverallScore();

        $this->appraisal->update([
            'status'        => 'approved',
            'hr_comments'   => $this->hrComments ?: null,
            'overall_score' => $result['score'],
            'overall_grade' => $result['grade'],
            'approved_by'   => auth()->id(),
            'approved_at'   => now(),
        ]);

        $this->showApproveModal = false;
        $this->appraisal->refresh();
        session()->flash('success', 'Appraisal approved and score calculated.');
    }

    public function reject(): void
    {
        $this->validate(['rejectionReason' => 'required|string|min:5']);

        $this->appraisal->update([
            'status'      => 'rejected',
            'hr_comments' => $this->rejectionReason,
        ]);

        $this->showRejectModal = false;
        $this->appraisal->refresh();
        session()->flash('success', 'Appraisal sent back for revision.');
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.appraisals.show');
    }
}
