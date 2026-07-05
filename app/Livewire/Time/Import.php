<?php

namespace App\Livewire\Time;

use App\Imports\AttendanceImport;
use App\Services\TimeManagementService;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;


class Import extends Component
{

    use WithFileUploads;

    public $importFile = null;

    public bool $markAbsent = false;

    public string $absentDate = '';

    public array $importErrors = [];

    public ?string $lastBatch = null;

    public ?int $processedCount = null;

    public bool $importing = false;

    public function importAttendance(): void
    {
        $this->validate([
            'importFile' => 'required|file|mimes:xlsx,xls|max:10240', // 10MB max

        ]);

        $this->importErrors = [];

        $this->processedCount = null;
        $batch = 'import_' . now()->format('dmYHis');
        $this->lastBatch = $batch;

        try{

        $import = new AttendanceImport($batch);
        Excel::import($import, $this->importFile->getRealPath());

        foreach ($import->getErrors() as $error) {
            $this->importErrors[] = $error;

        }

        $service = app(TimeManagementService::class);

        $this->processedCount = $service->processBatch($batch);

        $this->importFile = null;

         session()->flash('success', "Imported and processed {$this->processedCount} attendance record(s).");

        }catch (\Exception $e) {
            $this->importErrors[] = 'Import failed' . $e->getMessage();
        }
    }

    public function markMissingAbsent(): void
    {
        $this->validate([
            'absentDate' => 'required|date',
        ]);

       $count = app(TimeManagementService::class)->markMissingAsAbsent($this->absentDate);

        session()->flash('success', 'Marked ' . $count . ' employees as absent for ' . $this->absentDate = '');
    }


    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.time.import');
    }
}
