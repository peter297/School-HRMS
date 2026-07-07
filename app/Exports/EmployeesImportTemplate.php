<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Override;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EmployeesImportTemplate implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{


    public function title(): string{
        return 'Employee Import Template';
    }

     public function __construct()
    {
        //
    }
    public function array(): array{
        return[
            [
                '001',
                'AMINA',
                'HASSAN',
                'amina.hassan@gmail.com',
                '254712345678',
                'teacher',
                'junior_school',
                'juja_road',
                'Class Teacher',
                '01/09/2026',
                'female',
                '12345678',
                'A12345678Z',
                '123456789',
                'ABC123456789',
                'Gulf',
                '1122334455',
                'active',

            ],
        ];
    }

    #[Override]
    public function headings(): array
    {
        return[
            'staff_number',
            'first_name',
            'last_name',
            'email',
            'phone',
            'staff_type',
            'division',
            'branch',
            'job_title',
            'date_of_joining',
            'gender',
            'national_id',
            'kra_pin',
            'nssf_number',
            'sha_number',
            'bank_name',
            'bank_account_number',
            'status',
        ];
    }

    public function styles(Worksheet $sheet): array{
        $headingStyle = [
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1e3a5f']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ];

        $exampleStyle = [
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'eaf0fb']],
        ];

        return[
           1 => $headingStyle,
            2 => $exampleStyle,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 14, 'B' => 16, 'C' => 16, 'D' => 28,
            'E' => 18, 'F' => 24, 'G' => 20, 'H' => 14,
            'I' => 22, 'J' => 18, 'K' => 10, 'L' => 16,
            'M' => 10, 'N' => 18, 'O' => 20, 'P' => 22,
            'Q' => 14, 'R' => 20, 'S' => 20
        ];
    }

}
