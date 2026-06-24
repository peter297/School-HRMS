<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;

class EmployeesImportTemplate implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{


    public function title(): string{
        return 'Employee Import Template';
    }

    public function array(): array{
        return[
            [
                '001',
                'AMINA',
                'HASSAN',
                'amina.hassan@gmail.com',
                
            ],
        ];
    }
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
}
