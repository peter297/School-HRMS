<?php
// app/Livewire/Disciplinary/Show.php

namespace App\Livewire\Disciplinary;

use App\Models\DisciplinaryCase;
use App\Models\DisciplinaryDocument;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Show extends Component
{
    public DisciplinaryCase $disciplinaryCase;

    // Show cause letter modal
    public bool   $showShowCauseModal   = false;
    public string $showCauseDate        = '';
    public string $showCauseContent     = '';

    // Response modal
    public bool   $showResponseModal    = false;
    public string $responseText         = '';
    public string $responseDate         = '';

    // Hearing modal
    public bool   $showHearingModal     = false;
    public string $hearingDate          = '';
    public string $hearingNotes         = '';

    // Outcome modal
    public bool   $showOutcomeModal     = false;
    public string $outcome              = '';
    public string $outcomeNotes         = '';
    public string $outcomeDate          = '';

    // Warning letter modal
    public bool   $showWarningModal     = false;
    public string $warningDate          = '';
    public string $warningLevel         = '';
    public string $warningContent       = '';

    public function mount(DisciplinaryCase $disciplinaryCase): void
    {
        $this->disciplinaryCase = $disciplinaryCase->load([
            'employee', 'recordedBy', 'documents.createdBy',
            'showCauseLetter', 'responseToShowCause',
            'hearingRecord', 'warningLetter',
        ]);
        $this->showCauseDate = today()->format('Y-m-d');
        $this->hearingDate   = today()->format('Y-m-d');
        $this->warningDate   = today()->format('Y-m-d');
        $this->outcomeDate   = today()->format('Y-m-d');
        $this->responseDate  = today()->format('Y-m-d');
    }

    // ── Show cause letter ─────────────────────────────────────

    public function saveShowCause(): void
    {
        $this->validate([
            'showCauseDate'    => 'required|date',
            'showCauseContent' => 'nullable|string',
        ]);

        DisciplinaryDocument::create([
            'disciplinary_case_id' => $this->disciplinaryCase->id,
            'type'                 => 'show_cause_letter',
            'warning_level'        => 'none',
            'document_date'        => $this->showCauseDate,
            'content'              => $this->showCauseContent ?: $this->disciplinaryCase->description,
            'created_by'           => auth()->id(),
        ]);

        $this->disciplinaryCase->update(['stage' => 'show_cause_issued']);
        $this->showShowCauseModal = false;
        $this->disciplinaryCase->refresh()->load(['documents.createdBy', 'showCauseLetter']);
        session()->flash('success', 'Show cause letter created.');
    }

    // ── Response to show cause ────────────────────────────────

    public function saveResponse(): void
    {
        $this->validate([
            'responseText' => 'required|string|min:5',
            'responseDate' => 'required|date',
        ]);

        DisciplinaryDocument::create([
            'disciplinary_case_id' => $this->disciplinaryCase->id,
            'type'                 => 'response_to_show_cause',
            'warning_level'        => 'none',
            'document_date'        => $this->responseDate,
            'content'              => $this->responseText,
            'created_by'           => auth()->id(),
        ]);

        $this->disciplinaryCase->update(['stage' => 'response_received']);
        $this->showResponseModal = false;
        $this->disciplinaryCase->refresh()->load(['documents.createdBy', 'responseToShowCause']);
        session()->flash('success', 'Response recorded.');
    }

    // ── Hearing record ────────────────────────────────────────

    public function saveHearing(): void
    {
        $this->validate([
            'hearingDate'  => 'required|date',
            'hearingNotes' => 'required|string|min:10',
        ]);

        DisciplinaryDocument::create([
            'disciplinary_case_id' => $this->disciplinaryCase->id,
            'type'                 => 'hearing_record',
            'warning_level'        => 'none',
            'document_date'        => $this->hearingDate,
            'content'              => $this->hearingNotes,
            'created_by'           => auth()->id(),
        ]);

        $this->disciplinaryCase->update(['stage' => 'hearing_held']);
        $this->showHearingModal = false;
        $this->disciplinaryCase->refresh()->load(['documents.createdBy', 'hearingRecord']);
        session()->flash('success', 'Hearing record saved.');
    }

    // ── Outcome ───────────────────────────────────────────────

    public function saveOutcome(): void
    {
        $this->validate([
            'outcome'      => 'required|in:verbal_warning,written_warning,final_written_warning,dismissal,no_action',
            'outcomeNotes' => 'nullable|string',
            'outcomeDate'  => 'required|date',
        ]);

        $this->disciplinaryCase->update([
            'outcome'       => $this->outcome,
            'outcome_notes' => $this->outcomeNotes ?: null,
            'outcome_date'  => $this->outcomeDate,
            'stage'         => 'outcome_recorded',
        ]);

        $this->showOutcomeModal = false;
        $this->disciplinaryCase->refresh();
        session()->flash('success', 'Outcome recorded.');
    }

    // ── Warning letter ────────────────────────────────────────

    public function saveWarningLetter(): void
    {
        $this->validate([
            'warningDate'    => 'required|date',
            'warningLevel'   => 'required|in:verbal,written,final_written,dismissal',
            'warningContent' => 'nullable|string',
        ]);

        DisciplinaryDocument::create([
            'disciplinary_case_id' => $this->disciplinaryCase->id,
            'type'                 => 'warning_letter',
            'warning_level'        => $this->warningLevel,
            'document_date'        => $this->warningDate,
            'content'              => $this->warningContent ?: '',
            'created_by'           => auth()->id(),
        ]);

        $this->disciplinaryCase->update(['stage' => 'warning_issued']);
        $this->showWarningModal = false;
        $this->disciplinaryCase->refresh()->load(['documents.createdBy', 'warningLetter']);
        session()->flash('success', 'Warning letter created.');
    }

    // ── Close case ────────────────────────────────────────────

    public function closeCase(): void
    {
        $this->disciplinaryCase->update(['stage' => 'closed']);
        $this->disciplinaryCase->refresh();
        session()->flash('success', 'Case closed.');
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.disciplinary.show');
    }
}
