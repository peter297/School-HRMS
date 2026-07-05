<?php

namespace App\Livewire\Time;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Incident;

class Incidents extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filterType = '';

    public string $filterResolved = '';

    public string $filterMonth = '';

    public bool $showResolveModal = false;

    public int $resolvingId = 0;

    public string $resolutionNote = '';



    public  function openResolveModal(int $id): void
    {
        $this->resolvingId = $id;
        $this->resolutionNote = '';
        $this->showResolveModal = true;
    }

    public function confirmResolve(): void{
        $this->validate([
            'resolutionNote' => 'required|string|min:5',
        ]);

        Incident::findOrFail($this->resolvingId)->update([
            'resolved' => true,
            'resolution_note' => $this->resolutionNote,
            'resolved_by' => auth()->id(),
            'resolved_at' => now(),
        ]);

        $this->showResolveModal = false;
        session()->flash('success', 'Incident resolved successfully.');
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $incidents = Incident::with('employee')
            ->whereHas('employee', fn($q) =>
                $q->when($this->search, fn($q) =>
                    $q->where('first_name',    'like', "%{$this->search}%")
                      ->orWhere('last_name',   'like', "%{$this->search}%")
                      ->orWhere('staff_number','like', "%{$this->search}%")
                )
            )
            ->when($this->filterType, fn($q) => $q->where('type', $this->filterType))
            ->when($this->filterResolved === '0', fn($q) => $q->where('resolved', false))
            ->when($this->filterResolved === '1', fn($q) => $q->where('resolved', true))
            ->when($this->filterMonth,  fn($q) =>
                $q->whereMonth('date', substr($this->filterMonth, 5, 2))
                  ->whereYear('date',  substr($this->filterMonth, 0, 4))
            )
            ->latest('date')
            ->paginate(20);

            $unresolvedCount = Incident::unresolved()->count();
        return view('livewire.time.incidents', compact('incidents', 'unresolvedCount'));
    }
}
