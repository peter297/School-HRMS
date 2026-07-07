<?php

namespace App\Imports;


use App\Models\Employees;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EmployeesImport implements ToCollection, WithHeadingRow
{
    private array $errors   = [];
    private int   $imported = 0;

    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            try {
                $arr = $row->toArray();

                // Debug — remove after confirming
                // throw new \Exception('RAW ROW ' . $index . ': ' . json_encode($arr));

                $staffNumber = trim((string) ($arr['staff_number'] ?? ''));

                if (!$staffNumber) continue;

                // Skip existing staff numbers
                if (Employees::where('staff_number', $staffNumber)->exists()) {
                    continue;
                }

                $dateOfJoining = $this->parseDate($arr['date_of_joining'] ?? null);

                if (!$dateOfJoining) {
                    $this->errors[] = "Row {$index}: Invalid date for '{$staffNumber}'.";
                    continue;
                }

                Employees::create([
                    'staff_number'    => $staffNumber,
                    'first_name'      => trim($arr['first_name']   ?? ''),
                    'last_name'       => trim($arr['last_name']    ?? ''),
                    'email'           => trim($arr['email']        ?? '') ?: null,
                    'phone'           => trim($arr['phone']        ?? '') ?: null,
                    'staff_type'      => trim($arr['staff_type']   ?? ''),
                    'division'        => trim($arr['division']     ?? ''),
                    'branch'          => trim($arr['branch']       ?? ''),
                    'job_title'       => trim($arr['job_title']    ?? '') ?: null,
                    'date_of_joining' => $dateOfJoining,
                    'gender'          => trim($arr['gender']       ?? '') ?: null,
                    'national_id'     => trim($arr['national_id']  ?? '') ?: null,
                    'kra_pin'  => trim($arr['kra_pin'] ?? '') ?: null,
                    'nssf_number'  => trim($arr['nssf_number'] ?? '') ?: null,
                    'sha_number'  => trim($arr['sha_number'] ?? '') ?: null,
                    'bank_name'  => trim($arr['bank_name'] ?? '') ?: null,
                    'bank_account_number'  => trim($arr['bank_account_number'] ?? '') ?: null,
                    'status'          => trim($arr['status']       ?? 'active'),
                ]);

                $this->imported++;

            } catch (\Exception $e) {
                $this->errors[] = "Row {$index}: " . $e->getMessage();
            }
        }
    }

    public function getErrors(): array   { return $this->errors; }
    public function getImportedCount(): int { return $this->imported; }

    private function parseDate(mixed $value): ?string
    {
        if ($value === null || $value === '') return null;

        try {
            if (is_numeric($value)) {
                return \PhpOffice\PhpSpreadsheet\Shared\Date
                    ::excelToDateTimeObject((float) $value)
                    ->format('Y-m-d');
            }
            return \Carbon\Carbon::parse(trim((string) $value))->format('Y-m-d');
        } catch (\Exception) {
            return null;
        }
    }
}
