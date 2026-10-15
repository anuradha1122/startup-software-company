<?php

use App\Http\Controllers\Portal\ApprovalController;
use App\Http\Controllers\Portal\AuthController;
use App\Http\Controllers\Portal\DashboardController;
use App\Http\Controllers\Portal\DocumentController;
use App\Http\Controllers\Portal\InvoiceController;
use App\Http\Controllers\Portal\PasswordResetController;
use App\Http\Controllers\Portal\ProjectController;
use Illuminate\Support\Facades\Route;

Route::prefix('portal')
    ->name('portal.')
    ->group(function (): void {
        Route::middleware(
            'guest:client_portal'
        )->group(
            function (): void {
                Route::get(
                    'login',
                    [
                        AuthController::class,
                        'create',
                    ]
                )->name('login');

                Route::post(
                    'login',
                    [
                        AuthController::class,
                        'store',
                    ]
                )->name('login.store');

                Route::get(
                    'forgot-password',
                    [
                        PasswordResetController::class,
                        'request',
                    ]
                )->name(
                    'password.request'
                );

                Route::post(
                    'forgot-password',
                    [
                        PasswordResetController::class,
                        'email',
                    ]
                )->name(
                    'password.email'
                );

                Route::get(
                    'reset-password/{token}',
                    [
                        PasswordResetController::class,
                        'reset',
                    ]
                )->name(
                    'password.reset'
                );

                Route::post(
                    'reset-password',
                    [
                        PasswordResetController::class,
                        'update',
                    ]
                )->name(
                    'password.update'
                );
            }
        );

        Route::middleware([
            'auth:client_portal',
            'client.portal.active',
        ])->group(
            function (): void {
                Route::get(
                    '/',
                    DashboardController::class
                )->name(
                    'dashboard'
                );

                Route::post(
                    'logout',
                    [
                        AuthController::class,
                        'destroy',
                    ]
                )->name(
                    'logout'
                );

                Route::get(
                    'projects/{project}',
                    [
                        ProjectController::class,
                        'show',
                    ]
                )->name(
                    'projects.show'
                );

                Route::get(
                    'projects/{project}/pdf',
                    [
                        ProjectController::class,
                        'pdf',
                    ]
                )->name(
                    'projects.pdf'
                );

                Route::get(
                    'approvals',
                    [
                        ApprovalController::class,
                        'index',
                    ]
                )->name(
                    'approvals.index'
                );

                Route::get(
                    'approvals/{approval}',
                    [
                        ApprovalController::class,
                        'show',
                    ]
                )->name(
                    'approvals.show'
                );

                Route::post(
                    'approvals/{approval}/respond',
                    [
                        ApprovalController::class,
                        'respond',
                    ]
                )->name(
                    'approvals.respond'
                );

                Route::get(
                    'documents/{document}/download',
                    [
                        DocumentController::class,
                        'download',
                    ]
                )->name(
                    'documents.download'
                );

                Route::get(
                    'invoices',
                    [
                        InvoiceController::class,
                        'index',
                    ]
                )->name(
                    'invoices.index'
                );

                Route::get(
                    'invoices/{invoice}/pdf',
                    [
                        InvoiceController::class,
                        'pdf',
                    ]
                )->name(
                    'invoices.pdf'
                );
            }
        );
    });
