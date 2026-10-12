<?php

use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\CompanySettingController;
use App\Http\Controllers\Admin\LeadActivityController;
use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware([
    'auth',
    'verified',
    'active',
])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {

        Route::get(
            'users/export',
            [UserController::class, 'export']
        )
            ->middleware('permission:users.export')
            ->name('users.export');

        Route::get(
            'users',
            [UserController::class, 'index']
        )
            ->middleware('permission:users.view')
            ->name('users.index');

        Route::post(
            'users',
            [UserController::class, 'store']
        )
            ->middleware('permission:users.create')
            ->name('users.store');

        Route::get(
            'users/{user}/edit',
            [UserController::class, 'edit']
        )
            ->middleware('permission:users.update')
            ->name('users.edit');

        Route::put(
            'users/{user}',
            [UserController::class, 'update']
        )
            ->middleware('permission:users.update')
            ->name('users.update');

        Route::delete(
            'users/{user}',
            [UserController::class, 'destroy']
        )
            ->middleware('permission:users.delete')
            ->name('users.destroy');

        Route::get(
            'roles',
            [RoleController::class, 'index']
        )
            ->middleware('permission:roles.manage')
            ->name('roles.index');

        Route::post(
            'roles',
            [RoleController::class, 'store']
        )
            ->middleware('permission:roles.manage')
            ->name('roles.store');

        Route::put(
            'roles/{role}',
            [RoleController::class, 'update']
        )
            ->middleware('permission:roles.manage')
            ->name('roles.update');

        Route::delete(
            'roles/{role}',
            [RoleController::class, 'destroy']
        )
            ->middleware('permission:roles.manage')
            ->name('roles.destroy');

        Route::get(
            'company',
            [CompanySettingController::class, 'edit']
        )
            ->middleware('permission:company-settings.view')
            ->name('company.edit');

        Route::put(
            'company',
            [CompanySettingController::class, 'update']
        )
            ->middleware('permission:company-settings.update')
            ->name('company.update');

        Route::get(
            'audit',
            [AuditLogController::class, 'index']
        )
            ->middleware('permission:audit.view')
            ->name('audit.index');

        Route::get(
            'audit/excel',
            [AuditLogController::class, 'excel']
        )
            ->middleware('permission:audit.export')
            ->name('audit.excel');

        Route::get(
            'audit/pdf',
            [AuditLogController::class, 'pdf']
        )
            ->middleware('permission:audit.export')
            ->name('audit.pdf');

        Route::prefix('leads')
            ->name('leads.')
            ->group(function (): void {
                Route::get(
                    'export/excel',
                    [LeadController::class, 'excel']
                )
                    ->middleware(
                        'permission:leads.export'
                    )
                    ->name('excel');

                Route::get(
                    'export/pipeline-pdf',
                    [
                        LeadController::class,
                        'pipelinePdf',
                    ]
                )
                    ->middleware(
                        'permission:leads.export'
                    )
                    ->name('pipeline-pdf');

                Route::get(
                    '/',
                    [LeadController::class, 'index']
                )
                    ->middleware(
                        'permission:leads.view'
                    )
                    ->name('index');

                Route::get(
                    'create',
                    [LeadController::class, 'create']
                )
                    ->middleware(
                        'permission:leads.create'
                    )
                    ->name('create');

                Route::post(
                    '/',
                    [LeadController::class, 'store']
                )
                    ->middleware(
                        'permission:leads.create'
                    )
                    ->name('store');

                Route::get(
                    '{lead}',
                    [LeadController::class, 'show']
                )
                    ->middleware(
                        'permission:leads.view'
                    )
                    ->name('show');

                Route::get(
                    '{lead}/edit',
                    [LeadController::class, 'edit']
                )
                    ->middleware(
                        'permission:leads.update'
                    )
                    ->name('edit');

                Route::put(
                    '{lead}',
                    [LeadController::class, 'update']
                )
                    ->middleware(
                        'permission:leads.update'
                    )
                    ->name('update');

                Route::patch(
                    '{lead}/stage',
                    [
                        LeadController::class,
                        'updateStage',
                    ]
                )
                    ->middleware(
                        'permission:leads.update'
                    )
                    ->name('stage');

                Route::delete(
                    '{lead}',
                    [LeadController::class, 'destroy']
                )
                    ->middleware(
                        'permission:leads.delete'
                    )
                    ->name('destroy');

                Route::post(
                    '{lead}/activities',
                    [
                        LeadActivityController::class,
                        'store',
                    ]
                )
                    ->middleware(
                        'permission:leads.update'
                    )
                    ->name('activities.store');

                Route::post(
                    '{lead}/convert',
                    [
                        LeadController::class,
                        'convert',
                    ]
                )
                    ->middleware(
                        'permission:leads.convert'
                    )
                    ->name('convert');
            });
    });
