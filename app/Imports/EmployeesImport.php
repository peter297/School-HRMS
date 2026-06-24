<?php

namespace App\Imports;

use App\Models\Employees;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithSkipDuplicates;
use Maatwebsite\Excel\Concerns\WithValidation;
use Override;

class EmployeesImport implements
    ToModel,
    WithHeadingRow,
    WithValidation,
    SkipsOnError
{

    use SkipsErrors;


private array $imported = [];
    /**
     * Create a new class instance.
     */
  

    public function model(array $row): ?Employees
    {
        // Skip if staff_number already exists
        if (Employees::where('staff_number', $row['staff_number'])->exists()) {
            return null;
        }

        return new Employees([
            'staff_number' => $row['staff_number'],
            'first_name' => $row['first_name'],
            'last_name' => $row['last_name'],
            'email' => $row['email'] ?? null,
            'phone' => $row['phone']?? null,
            'staff_type' => $row['staff_type'],
            'division' => $row['division'],
            'branch' => $row['branch'],
            'job_title' => $row['job-title']??null,
           'date_of_joining' => $this->parseDate($row['date_of_joining']),
            'gender' => $row['gender']?? null,
            'national_id' => $row['national_id']?? null,
            'status' => $row['status'] ?? 'active',

        ]);
}


	public function rules(): array
    {
        return[
            'staff_number' => 'required|string',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'staff_type' => 'required|in:teacher,admin,spport_staff,',
            'division' => 'required|in:eye,upper_primary,junior_school',
            'branch' => 'required|in:juja_road,kitisuru,south_c',
            'date_of_joining' => 'required',

        ];
    }

    public function customValidationMessages(): array{
        return [
               'staff_type.in' => 'staff_type must be one of: teacher, admin, support_staff',
               'division.in' => 'division must be one of: eye, upper_primary, junior_school',
               'branch.in' => 'branch must be one of: juja_road, kitisuru, south_c',
        ];
    }

    public function parseDate(mixed $value): string{
        if(is_numeric($value)){
            return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)
            ->format('d-m-Y');
        }

        return \Carbon\Carbon::parse($value)->format('d-m-Y');
    }



}
