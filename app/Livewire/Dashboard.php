<?php

namespace App\Livewire;

use App\Models\AttendanceRecord;
use App\Models\AttendanceRecords;
use App\Models\Contract;
use App\Models\Employees;
use App\Models\Incident;

use App\Models\Leaves;

use App\Models\PermittedExits;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Dashboard extends Component
{

#[Layout('layouts.app')]
    public function render()
    {
        $today = today();
        $month = $today->month;
        $year  = $today->year;

        // ── Employees ────────────────────────────────────────────
        $totalEmployees  = Employees::count();
        $activeEmployees = Employees::where('employment_status', 'active')->count();
        $onLeave         = Employees::where('employment_status', 'on_leave')->count();

        $byBranch = Employees::where('employment_status', 'active')
            ->selectRaw('branch, count(*) as total')
            ->groupBy('branch')
            ->pluck('total', 'branch');

        $byType = Employees::where('employment_status', 'active')
            ->selectRaw('staff_type, count(*) as total')
            ->groupBy('staff_type')
            ->pluck('total', 'staff_type');

        // ── Contracts ─────────────────────────────────────────────
        $activeContracts   = Contract::where('status', 'active')->count();
        $expiringContracts = Contract::expiringSoon()->count();
        $expiredContracts  = Contract::where('status', 'expired')->count();

        // ── Leaves ────────────────────────────────────────────────
        $pendingLeaves  = Leaves::where('status', 'pending')->count();
        $approvedLeaves = Leaves::where('status', 'approved')
            ->whereMonth('start_date', $month)
            ->whereYear('start_date',  $year)
            ->count();

        $leaveThisMonth = Leaves::where('status', 'approved')
            ->whereMonth('start_date', $month)
            ->whereYear('start_date',  $year)
            ->whereHas('employee')
            ->latest()
            ->take(5)
            ->get();

        // ── Attendance (today) ────────────────────────────────────
        $todayPresent  = AttendanceRecords::whereDate('date', $today)
            ->where('status', 'present')->count();
        $todayLate     = AttendanceRecords::whereDate('date', $today)
            ->where('status', 'late')->count();
        $todayAbsent   = AttendanceRecords::whereDate('date', $today)
            ->where('status', 'absent')->count();
        $todayFlagged  = AttendanceRecords::whereDate('date', $today)
            ->where('flagged', true)->count();

        // ── Incidents ─────────────────────────────────────────────
        $openIncidents     = Incident::where('resolved', false)->count();
        $resolvedThisMonth = Incident::where('resolved', true)
            ->whereMonth('resolved_at', $month)
            ->whereYear('resolved_at',  $year)
            ->count();

        $recentIncidents = Incident::with('employee')
            ->where('resolved', false)
            ->whereHas('employee')
            ->latest()
            ->take(5)
            ->get();

        // ── 1–2PM Movements (this month) ─────────────────────────
        $movementsThisMonth = PermittedExits::whereMonth('date', $month)
            ->whereYear('date', $year)
            ->count();

        $frequentMovers = PermittedExits::whereMonth('date', $month)
            ->whereYear('date', $year)
            ->selectRaw('employee_id, count(*) as total')
            ->groupBy('employee_id')
            ->orderByDesc('total')
            ->take(3)
            ->whereHas('employee')
            ->get();

        return view('livewire.dashboard', compact(
            'totalEmployees', 'activeEmployees', 'onLeave',
            'byBranch', 'byType',
            'activeContracts', 'expiringContracts', 'expiredContracts',
            'pendingLeaves', 'approvedLeaves', 'leaveThisMonth',
            'todayPresent', 'todayLate', 'todayAbsent', 'todayFlagged',
            'openIncidents', 'resolvedThisMonth', 'recentIncidents',
            'movementsThisMonth', 'frequentMovers',
        ));
    }
}
