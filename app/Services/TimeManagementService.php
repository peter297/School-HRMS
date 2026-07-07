<?php

namespace App\Services;

use App\Models\AttendanceLogs;
use App\Models\AttendanceRecords;
use App\Models\Employees;
use App\Models\Incident;
use App\Models\Incidents;
use App\Models\PermittedExits;
use App\Models\Schedules;
use Carbon\Carbon;

class TimeManagementService
{
    /**
     * Create a new class instance.
     */

    public function processLog(AttendanceLogs $log) : AttendanceRecords
    {
        // Process the attendance log
        // Implement your logic here

        $employee = $log->employee;

        $schedule = Schedules::where('staff_type', $employee->staff_type)->first();

        if(!$schedule) {
            // Handle the case when no schedule is found for the employee's staff type
            return $this->markAbsent($log, $employee);
        }

        $date = $log->date;



        $expectedIn = Carbon::parse($date->format('Y-m-d') . ' ' . $schedule->expected_in);
        $expectedOut = Carbon::parse($date->format('Y-m-d') . ' ' . $schedule->expected_out);
        $graceIn = (clone $expectedIn)->addMinutes($schedule->grace_minutes);
        $graceOut = (clone $expectedOut)->addMinutes($schedule->grace_minutes);

        $checkIn = $log->check_in ? Carbon::parse($date->format('Y-m-d') . ' ' . $log->check_in) : null;
        $checkOut = $log->check_out ? Carbon::parse($date->format('Y-m-d') . ' ' . $log->check_out) : null;

        $permittedExit = PermittedExits::where('employee_id', $employee->id)
            ->whereDate('date', $date)
            ->first();

        $minutesLate = 0;
        $minutesEarly = 0;
        $isLate = false;
        $isEarly = false;

        if ($checkIn && $checkIn->gt($graceIn)) {
            $isLate = true;
            $minutesLate = $checkIn->diffInMinutes($expectedIn);
        }

        if ($checkOut && $checkOut->lt($graceOut)) {
            $exitHour = (int) $checkOut->format('H');
            $exitMin = (int) $checkOut->format('i');
            $inWindow = ($exitHour === 13 || ($exitHour === 14 && $exitMin === 0));

            if($inWindow && $permittedExit) {
                $isEarly = false;

            }else{
                $isEarly = true;
                $minutesEarly = $expectedOut->diffInMinutes($checkOut);
            }
        }

        $status = match(true){
            $isLate && $isEarly => 'Late and Early',
            $isLate => 'Late',
            $isEarly => 'early_departure',
            default => 'Present',
        };

        $flagged = $isLate || $isEarly;

        $record = AttendanceRecords::updateOrCreate(
            [
                'employee_id' => $employee->id,
                'date' => $date,
            ],
            [
                'attendance_log_id' => $log->id,
                'status' => $status,
                'minutes_late' => (int)$minutesLate,
                'minutes_early' => (int)$minutesEarly,
                'flagged' => $flagged,
            ]
        );

        if($flagged) {
            $details = $this->buildIncidentDetails($isLate, $isEarly, $minutesLate, $minutesEarly, $checkIn, $checkOut, $expectedIn, $expectedOut);

            Incident::updateOrCreate(
                [
                    'employee_id' => $employee->id,
                    'date' => $date,
                ],
                [
                    'type' => $status,
                    'minutes_late' => (int)$minutesLate,
                    'minutes_early' => (int)$minutesEarly,
                    'details' => $details,
                    'resolved' => false,
                ]
            );
        }

        return $record;

    }

    public function markAbsent(AttendanceLogs $log, Employees $employee): AttendanceRecords
    {
        $record = AttendanceRecords::updateOrCreate(
            [
                'employee_id' => $employee->id,
                'date' => $log->date,
            ],
            [
                'attendance_log_id' => $log->id,
                'status' => 'Absent',
                'minutes_late' => 0,
                'minutes_early' => 0,
                'flagged' => true,
            ]
        );

        Incident::updateOrCreate(
            [
                'employee_id' => $employee->id,
                'date' => $log->date,
            ],
            [
                'type' => 'Absent',
                'minutes_late' => 0,
                'minutes_early' => 0,
                'details' => 'Employee was absent on this date.',
                'resolved' => false,
            ]
        );

        Incident::updateOrCreate(
            [
                'employee_id' => $employee->id,
                'date' => $log->date,

            ],
            [
                'type' => 'Absent',
                'details' => 'Employee was absent on this date.',
            ]
        );

        return $record;
    }

    public function processBatch(string $batch): int{
        $logs = AttendanceLogs::where('import_batch', $batch)
        ->with('employee')
        ->get();

        foreach($logs as $log){
            $this->processLog($log);
        }

        return $logs->count();
    }

    public function markMissingAsAbsent(string $date): int{

    $carbon = Carbon::parse($date);
    if($carbon->isWeekend())return 0;

    $count = 0;

    Employees::where('employment_status', 'active')->each(function (Employees $employee) use ($date, &$count){
        $hasLog = AttendanceLogs::where('employee_id', $employee->id)
            ->whereDate('date', $date)
            ->exists();

        if(!$hasLog){
            $log = AttendanceLogs::create([
                'employee_id' => $employee->id,
                'date' => $date,
                'check_in' => null,
                'check_out' => null,
                'source' => 'system',
                'import_batch' => 'absent_' . $date,
            ]);

            $this->markAbsent($log, $employee);
            $count++;
        }
    });

    return $count;

    }

    public function buildIncidentDetails(bool $isLate, bool $isEarly, int $minutesLate, int $minutesEarly, ?Carbon $checkIn, ?Carbon $checkOut, Carbon $expectedIn, Carbon $expectedOut): string{
        $parts = [];

        if($isLate){
            $parts[] = "Late by {$minutesLate} minutes. Checked in at {$checkIn->format('H:i')} instead of expected {$expectedIn->format('H:i')}.";
        }

        if($isEarly){
            $parts[] = "Left early by {$minutesEarly} minutes. Checked out at {$checkOut->format('H:i')} instead of expected {$expectedOut->format('H:i')}.";
        }

        return implode(' ', $parts);
    }
}
