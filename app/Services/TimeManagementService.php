<?php

namespace App\Services;

use App\Models\AttendanceLogs;
use App\Models\AttendanceRecords;
use App\Models\Incidents;
use App\Models\PermittedExits;
use App\Models\Schedules;
use Carbon\Carbon;

class TimeManagementService
{
    /**
     * Create a new class instance.
     */

    public function processLog(AttendanceLogs $log) AttendanceRecords
    {
        // Process the attendance log
        // Implement your logic here

        $employee = $log->employee;

        $schedule = Schedules::where('staff_type', $employee->staff_type)->first();

        if(!schedule) {
            // Handle the case when no schedule is found for the employee's staff type
            return $this->markAbsent($log, $employee);
        }

        $date = $log->date;

        $expectedIn = Carbon::parse($date->format('d-m-Y') . ' ' . $schedule->expected_in);
        $expectedOut = Carbon::parse($date->format('d-m-Y') . ' ' . $schedule->expected_out);
        $graceIn = (clone $expectedIn)->addMinutes($schedule->grace_minutes);
        $graceOut = (clone $expectedOut)->addMinutes($schedule->grace_minutes);

        $checkIn = $log->check_in ? Carbon::parse($date->format('d-m-Y') . ' ' . $log->check_in) : null;
        $checkOut = $log->check_out ? Carbon::parse($date->format('d-m-Y') . ' ' . $log->check_out) : null;

        $permittedExit = PermittedExits::where('employee_id', $employee->id)
            ->whereDate('exit_date', $date)
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
                'minutes_late' => $minutesLate,
                'minutes_early' => $minutesEarly,   
                'flagged' => $flagged,
            ]
        );

        if($flagged) {
            $details = $this->buildIncidentDetails($isLate, $isEarly, $minutesLate, $minutesEarly, $checkIn, $checkOut, $expectedIn, $expectedOut);

            Incidents::updateOrCreate(
                [
                    'employee_id' => $employee->id,
                    'date' => $date,
                ],
                [
                    'type' => $status,
                    'minutes_late' => $minutesLate,
                    'minutes_early' => $minutesEarly,
                    'details' => $details,  
                    'resolved' => false,
                ]
            );
        }

        return $record;

    }
    public function __construct()
    {
        //
    }

    public function processBatch(string $batch): int{
        $logs = AttendanceLogs::where('batch', $batch)
        ->with('employee')
        ->get();

        foreach($logs as $log){
            $this->processLog($log);
        }

        return $logs->count();
    }

    public function markMissingAsAbsent(string $date): int{

    $carbon = Carbon::parse($date);
    if($carbon->isWeekend()){
        return 0;
    }
}
