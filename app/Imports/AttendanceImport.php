<?php

namespace App\Imports;


use App\Models\AttendanceLogs;
use App\Models\Employees;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AttendanceImport implements ToCollection, WithHeadingRow
{
    private array $errors = [];


    public function __construct(private string $batch) {}



    public function collection(Collection $rows): void
    {

    //      $firstRow = $rows->first();
    // throw new \Exception(
    //     'RAW ROW: ' . json_encode($firstRow->toArray())
    // );

        foreach ($rows as $row) {
            try {
                $staffNumber = trim((string) ($row['staff_number'] ?? ''));

                if (!$staffNumber) continue;

                $employee = Employees::where('staff_number', $staffNumber)->first();

                if (!$employee) {
                    $this->errors[] = "Staff number '{$staffNumber}' not found — row skipped.";
                    continue;
                }

                $date     = $this->parseDate($row['date']      ?? null);
                $checkIn  = $this->parseTime($row['check_in']  ?? null);
                $checkOut = $this->parseTime($row['check_out'] ?? null);

                if (!$date) {
                    $this->errors[] = "Invalid date for '{$staffNumber}' — row skipped.";
                    continue;
                }

                AttendanceLogs::updateOrCreate(
                    [
                        'employee_id' => $employee->id,
                        'date'        => $date,
                    ],
                    [
                        'check_in'     => $checkIn,
                        'check_out'    => $checkOut,
                        'source'       => 'biometric',
                        'import_batch' => $this->batch,
                    ]
                );

            } catch (\Exception $e) {
                $this->errors[] = 'Row error: ' . $e->getMessage();
            }
        }
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    private function parseDate(mixed $value): ?string
    {
        if ($value === null || $value === '') return null;

        try {
            // Excel date serial integer/float e.g. 46208
            // Excel date serial integer/float e.g. 46208
        if (is_numeric($value)) {
            return \PhpOffice\PhpSpreadsheet\Shared\Date
                ::excelToDateTimeObject((float) $value)
                ->format('Y-m-d');
        }

        $value = trim((string) $value);

        // Fix: If a double-date/time mess exists, extract just the first date match
        // Matches YYYY-MM-DD or DD-MM-YYYY formats
        if (preg_match('/(\d{4}-\d{2}-\d{2})|(\d{2}-\d{2}-\d{4})/', $value, $matches)) {
            $value = $matches[0]; // Drops the extra date and the time
        }

        // Plain date string e.g. "2026-07-05" or "26-06-2026"
        return \Carbon\Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception) {
            return null;
        }
    }

    // private function parseTime(mixed $value): ?string
    // {
    //     if ($value === null || $value === '') return null;

    //     try {
    //         // Excel time fraction e.g. 0.28125 = 06:45:00
    //         if (is_numeric($value)) {
    //             $float = (float) $value;

    //             // Strip date portion from datetime serial e.g. 46208.28125
    //             $fraction = $float - floor($float);

    //             $totalSeconds = (int) round($fraction * 86400);
    //             $h = intdiv($totalSeconds, 3600);
    //             $m = intdiv($totalSeconds % 3600, 60);
    //             $s = $totalSeconds % 60;

    //             return sprintf('%02d:%02d:%02d', $h, $m, $s);
    //         }

    //         // Plain time string e.g. "06:45" or "06:45:00"
    //         $value = trim((string) $value);

    //         // Extract HH:MM or HH:MM:SS from end of any string
    //         if (preg_match('/(\d{1,2}:\d{2}(:\d{2})?)$/', $value, $matches)) {
    //             [$h, $m, $s] = array_pad(explode(':', $matches[1]), 3, '00');
    //             return sprintf('%02d:%02d:%02d', (int)$h, (int)$m, (int)$s);
    //         }

    //         return null;

    //     } catch (\Exception) {
    //         return null;
    //     }
    // }

    private function parseTime(mixed $value): ?string
{
    if ($value === null || $value === '') return null;

    try {
        if (is_numeric($value)) {
            $float = (float) $value;
            $fraction = $float - floor($float);
            $totalSeconds = (int) round($fraction * 86400);

            $h = intdiv($totalSeconds, 3600);
            $m = intdiv($totalSeconds % 3600, 60);
            $s = $totalSeconds % 60;

            return sprintf('%02d:%02d:%02d', $h, $m, $s);
        }

        $value = trim((string) $value);

        // Extract the time pattern (HH:MM:SS or HH:MM) from anywhere in the string
        if (preg_match('/(\d{1,2}):(\d{2})(?::(\d{2}))?/', $value, $matches)) {
            $h = (int)$matches[1];
            $m = (int)$matches[2];
            $s = isset($matches[3]) ? (int)$matches[3] : 0;

            return sprintf('%02d:%02d:%02d', $h, $m, $s);
        }

        return null;
    } catch (\Exception) {
        return null;
    }
}
}
