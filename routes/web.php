<?php

use App\Livewire\Dashboard;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('login'));

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    // HR Admin and Supper Admin Routes
    Route::middleware('role:hr_admin,super_admin')->group(function () {
        Route::get('/employees', fn() => view('employees.index'))->name('employees.index');
        Route::get('/employees/create', fn() => view('employees.create'))->name('employees.create');
        Route::get('/employees/{employee}/edit', fn() => view('employees.edit'))->name('employees.edit');

    });
});
