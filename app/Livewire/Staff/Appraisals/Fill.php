<?php
// app/Livewire/Staff/Appraisals/Fill.php

namespace App\Livewire\Staff\Appraisals;

use App\Models\Appraisal;
use App\Models\AppraisalKpi;
use App\Services\AppraisalKpiService;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Fill extends Component
{
    public Appraisal $appraisal;

    public array  $ratings     = [];
    public array  $comments    = [];
    public string $managerComments   = '';
    public string $recommendations   = '';

    public function mount(Appraisal $appraisal): void
    {
        // Ensure only line manager of this employee can access
        $employee      = auth()->user()->employee;
        $appraisalEmployee = $appraisal->employee;

        if ($appraisalEmployee->line_manager_id !== $employee?->id) {
            abort(403, 'You are not the line manager for this employee.');
        }

        $this->appraisal         = $appraisal->load(['employee', 'kpis']);
        $this->managerComments   = $appraisal->line_manager_comments ?? '';
        $this->recommendations   = $appraisal->recommendations ?? '';

        foreach ($appraisal->kpis as $kpi) {
            $this->ratings[$kpi->id]  = $kpi->rating  ?? '';
            $this->comments[$kpi->id] = $kpi->comments ?? '';
        }
    }

    protected function rules(): array
    {
        $rules = [
            'managerComments' => 'nullable|string|max:2000',
            'recommendations' => 'nullable|string|max:2000',
        ];

        foreach ($this->appraisal->kpis as $kpi) {
            $rules["ratings.{$kpi->id}"]  = 'required|in:exceeds_expectations,meets_expectations,below_expectations';
            $rules["comments.{$kpi->id}"] = 'nullable|string|max:500';
        }

        return $rules;
    }

    #[Layout('components.layouts.staff')]
    public function submit(): void
    {
        $this->validate();

        $service = app(AppraisalKpiService::class);

        foreach ($this->appraisal->kpis as $kpi) {
            $rating = $this->ratings[$kpi->id];
            $score  = $service->ratingToScore($rating);

            $kpi->update([
                'rating'   => $rating,
                'score'    => $score,
                'comments' => $this->comments[$kpi->id] ?: null,
            ]);
        }

        $this->appraisal->update([
            'line_manager_comments' => $this->managerComments ?: null,
            'recommendations'       => $this->recommendations ?: null,
            'status'                => 'submitted',
            'submitted_by'          => auth()->id(),
            'submitted_at'          => now(),
        ]);

        session()->flash('success', 'Appraisal submitted to HR for approval.');
        $this->redirect(route('staff.appraisals.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.staff.appraisals.fill');
    }
}
