<?php
// app/Livewire/Disciplinary/DownloadPdf.php

namespace App\Livewire\Disciplinary;

use App\Models\DisciplinaryCase;
use App\Models\DisciplinaryDocument;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Livewire\Component;

class DownloadPdf extends Component
{
    public function mount(DisciplinaryCase $disciplinaryCase, DisciplinaryDocument $document): Response
    {
        $case = $disciplinaryCase->load('employee');
        $doc  = $document;

        $view = match($doc->type) {
            'show_cause_letter'     => 'pdf.show-cause-letter',
            'warning_letter'        => 'pdf.warning-letter',
            'hearing_record'        => 'pdf.hearing-record',
            default                 => 'pdf.show-cause-letter',
        };

        $pdf = Pdf::loadView($view, compact('case', 'document'))
            ->setPaper('a4', 'portrait');

        $filename = "{$case->case_number}-{$doc->type}-{$doc->document_date->format('Y-m-d')}.pdf";

        return $pdf->download($filename);
    }

    public function render() {}
}
