<?php

use App\Livewire\Dashboard;
use App\Livewire\Employees\Create;
use App\Livewire\Employees\Edit;
use App\Livewire\Employees\Index as EmployeesIndex;
use App\Livewire\Leaves\Index;
use App\Livewire\Staff\Approvals\Index as ApprovalsIndex;
use App\Livewire\Staff\Dashboard as StaffDashboard;
use App\Livewire\Staff\Leaves\Create as LeavesCreate;
use App\Livewire\Staff\Leaves\Index as LeavesIndex;
use App\Livewire\Time\Attendance;
use App\Livewire\Time\Import;
use App\Livewire\Time\Incidents;
use App\Livewire\Time\Movements;
// use App\Models\Schedules;
use App\Livewire\Teachers\Dashboard as TeacherDashboard;
use App\Livewire\Users\LinkEmpolyees;
use Illuminate\Support\Facades\Route;

Route::get('/welcome', function () {
    return view('welcome');
});

Route::get('/', fn() => redirect()->route('login'));



Route::middleware('auth')->group(function () {

    // Route::get('/dashboard', function () {
    //     return match (auth()->user()->role) {
    //         'super_admin', 'hr_admin' => redirect()->route('dashboard'),
    //         default  => redirect()->route('staff.dashboard'),
    //     };
    // })->middleware('auth')->name('login.redirect');

     Route::get('/login-redirect', function () {
        return match(auth()->user()->role) {
            'super_admin', 'hr_admin' => redirect()->route('dashboard'),
            default                   => redirect()->route('staff.dashboard'),
        };
    })->name('login.redirect');

    Route::prefix('staff')->name('staff.')->group(function () {
        Route::get('/dashboard', StaffDashboard::class)->name('dashboard');
        Route::get('/leaves', LeavesIndex::class)->name('leaves.index');
        Route::get('/leaves/apply', LeavesCreate::class)->name('leaves.create');

        Route::get('/approvals', ApprovalsIndex::class)->name('approvals.index');
    });

    Route::get('/dashboard', Dashboard::class)->name('dashboard');


    // HR Admin and Super Admin Routes
    Route::middleware('role:hr_admin,super_admin')->group(function () {

        Route::get('/employees', EmployeesIndex::class)->name('employees.index');
        Route::get('/employees/create', Create::class)->name('employees.create');
        Route::get('/employees/{employee}/edit', Edit::class)->name('employees.edit');

        Route::get('/contracts', App\Livewire\Contracts\Index::class)->name('contracts.index');
        Route::get('/contracts/create', \App\Livewire\Contracts\Create::class)->name('contracts.create');
        Route::get('/contracts/{contract}/edit', \App\Livewire\Contracts\Edit::class)->name('contracts.edit');



        Route::get('/leaves', Index::class)->name('leaves.index');
        Route::get('/leaves/create',      \App\Livewire\Leaves\Create::class)->name('leaves.create');
        Route::get('/leaves/{leave}',     \App\Livewire\Leaves\Show::class)->name('leaves.show');

        Route::get('/time/import', Import::class)->name('time.import');
        Route::get('/time/attendance', Attendance::class)->name('time.attendance');
        Route::get('/time/incidents', Incidents::class)->name('time.incidents');
        Route::get('/time/movements', Movements::class)->name('time.movements');

        // Route::get('/time/schedules', Schedules::class)->name('time.schedules');


    });

    Route::middleware(['role:super_admin'])->group(function() {
        Route::get('/users/link-employees', LinkEmpolyees::class)->name('users.link-employees');
    });
});

require __DIR__ . '/settings.php';
