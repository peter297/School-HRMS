<?php


namespace App\Livewire\Resignations;

use App\Models\Resignation;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Show extends Component
{
    public Resignation $resignation;

    public function mount(Resignation $resignation): void
    {
        $this->resignation = $resignation->load(['employee', 'recordedBy']);
    }

    public function markStage(string $stage): void
    {
        $updates = [];

        match($stage) {
            'notice_served' => $updates = [
                'notice_served'      => true,
                'notice_served_date' => now()->toDateString(),
                'status'             => 'notice_served',
            ],
            'last_working_day' => $updates = [
                'last_working_day_confirmed'      => true,
                'last_working_day_confirmed_date' => now()->toDateString(),
                'status'                          => 'last_working_day',
            ],
            'clearance_done' => $updates = [
                'clearance_done'      => true,
                'clearance_done_date' => now()->toDateString(),
                'status'              => 'clearance_done',
            ],
            'final_pay_processed' => $updates = [
                'final_pay_processed'      => true,
                'final_pay_processed_date' => now()->toDateString(),
                'status'                   => 'final_pay_processed',
            ],
            default => null,
        };

        if (!empty($updates)) {
            // Check if all stages done → mark completed
            $this->resignation->update($updates);
            $this->resignation->refresh();

            if (
                $this->resignation->notice_served &&
                $this->resignation->last_working_day_confirmed &&
                $this->resignation->clearance_done &&
                $this->resignation->final_pay_processed
            ) {
                $this->resignation->update(['status' => 'completed']);
            }

            $this->resignation->refresh();
            session()->flash('success', 'Stage updated successfully.');
        }
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.resignations.show');
    }
}
