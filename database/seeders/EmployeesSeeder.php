<?php
namespace Database\Seeders;

use App\Models\Employees;
use Illuminate\Database\Seeder;

class EmployeesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employees = [
            ['SCH-001', 'Mary', 'Wanjiku', 'teacher', 'eye', 'EYE Class Teacher', '123', '2021-01-10', '1234'],
            ['SCH-002', 'James', 'Otieno', 'teacher', 'upper_primary', 'Upper Primary Teacher', '1234', '2020-03-15', '12345'],
            ['SCH-003', 'Grace', 'Muthoni', 'teacher', 'junior_school', 'Junior School Teacher', '12345', '2019-08-01', '321'],
            ['SCH-004', 'David', 'Kamau', 'admin', 'administration', 'HR Officer', '3456', '2018-06-20', '4321'],
            ['SCH-005', 'Sarah', 'Njeri', 'support_staff', 'support_services', 'Kitchen Staff', '789', '2022-02-14', '568'],
            ['SCH-006', 'Peter', 'Odhiambo', 'support_staff', 'support_services', 'Security Officer', '793', '2020-11-05', '896'],
            ['SCH-007', 'Abdullahi', 'Omar', 'support_staff', 'support_services', 'Security Officer', '794', '2020-11-05'],

        ];

        foreach ($employees as [$number, $first, $last, $type, $division, $title, $id, $joined]) {
            Employees::updateOrCreate(
                ['staff_number' => $number],
                [
                    'first_name' => $first,
                    'last_name'  => $last,
                    'email'      => strtolower("{$first}.{$last}@school.ac.ke"),
                    'staff_type'        => $type,
                    'division'          => $division,
                    'job_title'         => $title,
                    'date_of_joining'   => $joined,
                    'national_id'       => $id,
                    'employment_status' => 'active',
                ]
            );
        }
    }

}
