<?php

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

require __DIR__.'/settings.php';
require __DIR__.'/admin.php';
