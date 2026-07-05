<?php

namespace Database\Seeders;

use App\Models\Schedules;
use Illuminate\Database\Seeder;

class SchedulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $schedules = [
            ['teacher',           '06:45', '16:10', 5],
            ['admin',                 '08:00', '17:00', 5],
            ['support_staff',         '06:45', '17:00', 5],
        ];

        foreach ($schedules as [$type, $in, $out, $grace]) {
            Schedules::updateOrCreate(
                ['staff_type' => $type],
                ['expected_in' => $in, 'expected_out' => $out, 'grace_minutes' => $grace]
            );
        }
    }
}
