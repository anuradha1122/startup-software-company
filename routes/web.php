<?php

use App\Http\Controllers\Employee\AttendanceController;
use App\Http\Controllers\Employee\LeaveController;
use App\Http\Controllers\Employee\PayslipController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return redirect()->route('login');
})->name('home');

Route::middleware([
    'auth',
    'verified',
    'active',
])->group(function (): void {
    Route::get(
        'dashboard',
        fn () => Inertia::render('dashboard')
    )->name('dashboard');

    Route::get(
        'notifications',
        [NotificationController::class, 'index']
    )->name('notifications.index');

    Route::patch(
        'notifications/read-all',
        [NotificationController::class, 'readAll']
    )->name('notifications.read-all');

    Route::patch(
        'notifications/{notification}/read',
        [NotificationController::class, 'read']
    )->name('notifications.read');
});

Route::middleware([
    'auth',
    'verified',
])->prefix('employee')
    ->name('employee.')
    ->group(function (): void {
        Route::get(
            'leave',
            [LeaveController::class, 'index']
        )
            ->middleware(
                'permission:leave-requests.view-own'
            )
            ->name('leave.index');

        Route::post(
            'leave',
            [LeaveController::class, 'store']
        )
            ->middleware(
                'permission:leave-requests.create'
            )
            ->name('leave.store');

        Route::get(
            'attendance',
            [AttendanceController::class, 'index']
        )
            ->middleware(
                'permission:attendance.view-own'
            )
            ->name('attendance.index');

        Route::post(
            'attendance/clock-in',
            [AttendanceController::class, 'clockIn']
        )
            ->middleware(
                'permission:attendance.clock'
            )
            ->name('attendance.clock-in');

        Route::post(
            'attendance/clock-out',
            [AttendanceController::class, 'clockOut']
        )
            ->middleware(
                'permission:attendance.clock'
            )
            ->name('attendance.clock-out');

        Route::get(
            'payslips',
            [PayslipController::class, 'index']
        )
            ->middleware(
                'permission:payslips.view-own'
            )
            ->name('payslips.index');

        Route::get(
            'payslips/{entry}/pdf',
            [PayslipController::class, 'pdf']
        )
            ->middleware(
                'permission:payslips.download-own'
            )
            ->name('payslips.pdf');
    });

require __DIR__.'/settings.php';
require __DIR__.'/admin.php';
require __DIR__.'/portal.php';
