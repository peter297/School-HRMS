<?php

namespace App\Exports;

use App\Models\Employees;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class EmployeesExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private ?array $ids = null,
        private string $search = '',
        private string $filterType = '',
        private string $filterDivision = '',
        private string $filterStatus = '',
        private string $filterBranch = '',

    )
    {}

    public function title(): string{
        return 'Employees';
    }

    public function query(){
        return Employees::query()
            ->when($this->ids,  fn($q) => $q->whereIn('id', $this->ids))
            ->when($this->search, fn($q) =>
                $q->where(fn($q) =>
                    $q->where('first_name',   'like', "%{$this->search}%")
                      ->orWhere('last_name',   'like', "%{$this->search}%")
                      ->orWhere('staff_number','like', "%{$this->search}%")
                )
            )
            ->when($this->filterType,     fn($q) => $q->where('staff_type', $this->filterType))
            ->when($this->filterDivision, fn($q) => $q->where('division',   $this->filterDivision))
            ->when($this->filterStatus,   fn($q) => $q->where('status',     $this->filterStatus))
            ->when($this->filterBranch,   fn($q) => $q->where('branch',     $this->filterBranch))
            ->orderBy('first_name');
    }

    public function headings(): array{
        return[
            'Staff Number',
            'First Name',
            'Last Name',
            'Email',
            'Phone',
            'Gender',
            'National ID',
            // 'Staff Type',
            // 'Division',
            'Branch',
            'Job Title',
            'Qualification',
            'TSC Number',
            'Date of Joining',
            'Date of Birth',
            'Years of Employment',
            'Age',
            'KRA PIN',
            'NSSF Number',
            'SHA Number',
            'Bank Name',
            'Bank Account Number',
            'Bank Code',
            'Branch Code',
            'Status',
        ];
    }

    public function map($employee):array{

    return [
        $employee->staff_number,
        $employee->first_name,
        $employee->last_name,
        $employee->email ?? '',
        $employee->phone ?? '',
        $employee->gender ?? '',
        $employee->national_id ?? '',
        // $employee->staff_type_label,
        // $employee->division_label,
        $employee->branch_label,
        $employee->job_title ?? '',
        $employee->qualification ?? '',
        $employee->tsc_number ?? '',
        $employee->date_of_joining->format('d/m/Y'),
        $employee->years_of_employment ?? '',
        $employee->date_of_birth ? $employee->date_of_birth->format('d/m/Y') : '',
        $employee->age ?? '',
        $employee->kra_pin ?? '',
        $employee->nssf_number ?? '',
        $employee->sha_number ?? '',
        $employee->bank_name ?? '',
        $employee->bank_account_number ?? '',
        $employee->bank_code ?? '',
        $employee->branch_code ?? '',
        ucfirst(str_replace('-', ' ', $employee->status)),

    ];
    }

    public function styles(Worksheet $sheet){
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1e3a5f']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function columnWidths(): array{
        return [
            'A' => 14,
            'B' => 18,
            'C' => 18,
            'D' => 28,
            'E' => 16,
            'F' => 10,
            'G' => 16,
            // 'H' => 24,
            // 'I' => 20,
            'J' => 14,
            'K' => 22,
            'L' => 16,
            'M' => 12,
            'N' => 18,
            'O' => 18,
            'P' => 18,
            'Q' => 18,
            'R' => 28,
            'S' => 14,
            'T' => 18,
            'U' => 18,
            'V' => 18,
            'W' => 18,

        ];
    }
}
