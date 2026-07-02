<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SchedulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $schedules = [

            [
                'staff_type' => 'teacher',
                'expected_in' => '06:45:00',
                'expected_out' => '16:10:00',
                'grace_minutes' => 5,
            ],
            [
                'staff_type' => 'admin',
                'expected_in' => '08:00:00',
                'expected_out' => '17:00:00',
                'grace_minutes' => 5,
            ],
            [
                'staff_type' => 'support_staff',
                'expected_in' => '06:45:00',
                'expected_out' => '17:00:00',
                'grace_minutes' => 5,
            ],
        ];

        foreach($schedules as [$type, $in, $out, $grace]) {
            \App\Models\Schedules::updateOrCreate([
                'staff_type' => $type,
                'expected_in' => $in,
                'expected_out' => $out,
                'grace_minutes' => $grace,
            ]);
        }
    }
}
