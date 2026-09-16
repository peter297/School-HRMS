<?php
// app/Livewire/Terminations/Show.php

namespace App\Livewire\Terminations;

use App\Models\Termination;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Show extends Component
{
    public Termination $termination;

    public function mount(Termination $termination): void
    {
        $this->termination = $termination->load(['employee', 'recordedBy']);
    }

    public function markStage(string $stage): void
    {
        $updates = match($stage) {
            'notice_served' => [
                'notice_served'      => true,
                'notice_served_date' => now()->toDateString(),
                'status'             => 'notice_served',
            ],
            'clearance_done' => [
                'clearance_done'      => true,
                'clearance_done_date' => now()->toDateString(),
                'status'              => 'clearance_done',
            ],
            'final_pay_processed' => [
                'final_pay_processed'      => true,
                'final_pay_processed_date' => now()->toDateString(),
            ],
            default => [],
        };

        if (!empty($updates)) {
            $this->termination->update($updates);
            $this->termination->refresh();

            // Auto complete when all stages done
            if (
                $this->termination->notice_served &&
                $this->termination->clearance_done &&
                $this->termination->final_pay_processed
            ) {
                $this->termination->update(['status' => 'completed']);
            }

            $this->termination->refresh();
            session()->flash('success', 'Stage updated successfully.');
        }
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.terminations.show');
    }
}
