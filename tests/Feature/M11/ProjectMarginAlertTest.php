<?php

use App\Models\Project;
use App\Models\ProjectFinanceSetting;
use App\Models\User;
use App\Services\Finance\ProjectMarginAlertService;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Notification::fake();

    $this->seed(
        RolePermissionSeeder::class
    );
});

it('queues margin warning when project falls below warning threshold', function (): void {
    $admin =
        User::factory()->create();

    $admin->assignRole('Admin');

    $project =
        Project::factory()->create([
            'currency' => 'LKR',

            'contract_value' => 100000,

            'status' => 'active',
        ]);

    ProjectFinanceSetting::create([
        'project_id' => $project->id,

        'target_margin_percent' => 80,

        'warning_margin_percent' => 80,

        'alerts_enabled' => true,

        'created_by' => $admin->id,
    ]);

    $sent =
        app(
            ProjectMarginAlertService::class
        )->check(
            $project
        );

    expect($sent)->toBeFalse();
});
