<?php

namespace Database\Seeders;

use App\Models\LeaveTypes;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LeaveTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $types = [
            ['Annual Leave',      'AL',  21, true,  true,  'Standard paid annual leave'],
            ['Sick Leave',        'SL',  10, true,  true,  'Medical/illness leave'],
            ['Maternity Leave',   'ML',  90, true,  true,  'Paid maternity leave'],
            ['Paternity Leave',   'PL',  14, true,  true,  'Paid paternity leave'],
            ['Compassionate',     'CL',   5, true,  false, 'Bereavement or family emergency'],
            ['Study Leave',       'STL',  5, false, true,  'Exam or study purposes'],
            ['Unpaid Leave',      'UL',   0, false, true,  'Leave without pay'],
        ];

        foreach ($types as [$name, $code, $days, $paid, $approval, $desc]) {
            LeaveTypes::updateOrCreate(
                ['code' => $code],
                [
                    'name'              => $name,
                    'days_allowed'      => $days,
                    'is_paid'           => $paid,
                    'requires_approval' => $approval,
                    'description'       => $desc,
                    'active'            => true,
                ]
            );
        }
    }
}
