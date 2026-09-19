<?php


namespace App\Livewire\Appraisals;

use App\Models\Appraisal;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Livewire\Component;

class DownloadPdf extends Component
{
    public function mount(Appraisal $appraisal): Response
    {
        $appraisal->load(['employee', 'kpis', 'submittedBy', 'approvedBy']);

        $pdf = Pdf::loadView('pdf.appraisal', compact('appraisal'))
            ->setPaper('a4', 'portrait');

        $filename = "appraisal-{$appraisal->employee->staff_number}-{$appraisal->year}-{$appraisal->type}.pdf";

        return $pdf->download($filename);
    }

    public function render() {}
}
