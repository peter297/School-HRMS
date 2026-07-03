<?php

namespace App\Imports;

use App\Models\AttendanceLogs;
use App\Models\Employees;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AttendanceImport implements ToModel, WithHeadingRow, SkipsOnError
{

    use SkipsOnError;

    public function __construct(private string $batch){}

    /**
     * Create a new class instance.
     */

    public function model(array $row): ?AttendanceLogs
    {
        $employee = Employees::where('staff_number', $row['staff_number'])->first();

        if(!$employee){
            return null;
        }
        $date = $this->parseDate($row['date']);
        $checkIn = $this->parseTime($row['check_in'] ?? null);
        $checkOut = $this->parseTime($row['check_out'] ?? null);

        return new AttendanceLogs([
            'employee_id' => $employee->id,
            'date' => $date,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'source' => 'biometric',
            'import_batch' => $this->batch,
        ]);
    }

    private function parseDate(mixed $value): ?string{
        if(!$value){
            return null;
        }

        try{
            if(is_numeric($value)){
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('d-m-Y');
            }

            return Carbon::parse($value)->format('d-m-Y');
        }catch(\Exception $e){
            return null;
        }

    }

    private function parseTime(mixed $value): ?string{
        if(!$value){
            return null;
        }

        try{
            if(is_numeric($value)){
                $totalSeconds = (int) round($value * 86400);
                $h = intdiv($totalSeconds, 3600);
                $m = intdiv($totalSeconds % 3600, 60);
                $s = $totalSeconds % 60;
                return sprintf('%02d:%02d:%02d', $h, $m, $s);
            }

            return Carbon::parse($value)->format('H:i:s');
        }catch(\Exception $e){
            return null;
        }
    }
}
