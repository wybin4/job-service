<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/admin/dashboard', [DashboardController::class, 'adminDashboard'])->middleware(['auth:admin'])->name('admin.dashboard');
Route::get('/university/dashboard', [DashboardController::class, 'universityDashboard'])->middleware(['auth:university'])->name('university.dashboard');
Route::get('/student/dashboard', [DashboardController::class, 'studentDashboard'])->middleware(['auth:student'])->name('student.dashboard');
Route::get('/employer/dashboard', [DashboardController::class, 'employerDashboard'])->middleware(['auth:employer'])->name('employer.dashboard');