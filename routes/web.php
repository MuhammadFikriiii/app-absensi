<?php

use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\Admin\AttendanceCorrectionController as AdminAttendanceCorrectionController;
use App\Http\Controllers\Admin\AttendanceSettingController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\LeaveRequestController as AdminLeaveRequestController;
use App\Http\Controllers\Admin\RecapController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceCorrectionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\CoworkerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
    Route::post('/attendance/clock-in', [AttendanceController::class, 'clockIn'])->name('attendance.clock-in');
    Route::post('/attendance/clock-out', [AttendanceController::class, 'clockOut'])->name('attendance.clock-out');
    Route::post('/attendance/break/start', [AttendanceController::class, 'startBreak'])->name('attendance.break.start');
    Route::post('/attendance/break/end', [AttendanceController::class, 'endBreak'])->name('attendance.break.end');
    Route::get('/calendar', CalendarController::class)->name('calendar');
    Route::get('/history', HistoryController::class)->name('history');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/coworkers', CoworkerController::class)->name('coworkers');
    Route::get('/leaves', [LeaveRequestController::class, 'index'])->name('leaves.index');
    Route::get('/leaves/create', [LeaveRequestController::class, 'create'])->name('leaves.create');
    Route::post('/leaves', [LeaveRequestController::class, 'store'])->name('leaves.store');
    Route::get('/corrections', [AttendanceCorrectionController::class, 'index'])->name('corrections.index');
    Route::get('/attendances/{attendance}/correction', [AttendanceCorrectionController::class, 'create'])->name('corrections.create');
    Route::post('/attendances/{attendance}/correction', [AttendanceCorrectionController::class, 'store'])->name('corrections.store');

    Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
        Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
        Route::get('/employees/create', [EmployeeController::class, 'create'])->name('employees.create');
        Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
        Route::get('/attendances', AdminAttendanceController::class)->name('attendances.index');
        Route::get('/schedules', [ScheduleController::class, 'edit'])->name('schedules.edit');
        Route::put('/schedules', [ScheduleController::class, 'update'])->name('schedules.update');
        Route::get('/attendance-settings', [AttendanceSettingController::class, 'edit'])->name('attendance-settings.edit');
        Route::put('/attendance-settings', [AttendanceSettingController::class, 'update'])->name('attendance-settings.update');
        Route::get('/leaves', [AdminLeaveRequestController::class, 'index'])->name('leaves.index');
        Route::put('/leaves/{leaveRequest}', [AdminLeaveRequestController::class, 'update'])->name('leaves.update');
        Route::get('/corrections', [AdminAttendanceCorrectionController::class, 'index'])->name('corrections.index');
        Route::put('/corrections/{attendanceCorrection}', [AdminAttendanceCorrectionController::class, 'update'])->name('corrections.update');
        Route::get('/recaps', RecapController::class)->name('recaps.index');
        Route::get('/audit-logs', AuditLogController::class)->name('audit-logs.index');
    });
});
