<?php

use App\Livewire\Dashboard;
use App\Livewire\Leaves\Index;
use App\Livewire\Time\Attendance;
use App\Livewire\Time\Import;
use App\Livewire\Time\Incidents;
use App\Livewire\Time\Movements;
use Illuminate\Support\Facades\Route;

<<<<<<< HEAD
Route::get('/', fn() => redirect()->route('login'));
=======
Route::get('/welcome', function () {
    return view('welcome');
});

Route::get('/', fn() => redirect()->route('login'));

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    // HR Admin and Supper Admin Routes
    Route::middleware('role:hr_admin,super_admin')->group(function () {
        Route::get('/employees', fn() => view('employees.index'))->name('employees.index');
        Route::get('/employees/create', fn() => view('employees.create'))->name('employees.create');
        Route::get('/employees/{employee}/edit', fn() => view('employees.edit'))->name('employees.edit');

        Route::get('/contracts', App\Livewire\Contracts\Index::class)->name('contracts.index');

        Route::get('/leaves', Index::class)->name('leaves.index');

        Route::get('/time/import', Import::class)->name('time.import');
        Route::get('/time/attendance', Attendance::class)->name('time.attendance');
        Route::get('/time/incidents', Incidents::class)->name('time.incidents');
        Route::get('/time/movements', Movements::class)->name('time.movements');

    });
});
>>>>>>> 35f46aafe4114eb1e82f96df7409b62d51d7df58
