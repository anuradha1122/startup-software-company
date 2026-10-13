<?php

use App\Models\Project;
use App\Models\TaskWorkflowStatus;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\TaskWorkflowStatusSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed([
        RolePermissionSeeder::class,
        TaskWorkflowStatusSeeder::class,
    ]);
});

it('exports project tasks to excel and pdf', function (): void {
    $admin =
        User::factory()->create();

    $admin->assignRole('Admin');

    $project =
        Project::factory()->create([
            'status' => 'active',
        ]);

    $status =
        TaskWorkflowStatus::where(
            'slug',
            'backlog'
        )->firstOrFail();

    $project
        ->tasks()
        ->create([
            'task_sequence' => 1,

            'task_number' => "{$project->project_number}-T0001",

            'workflow_status_id' => $status->id,

            'title' => 'Export task',

            'created_by' => $admin->id,
        ]);

    $this
        ->actingAs($admin)
        ->get(
            "/admin/projects/{$project->public_id}/tasks/export/excel"
        )
        ->assertOk();

    $this
        ->actingAs($admin)
        ->get(
            "/admin/projects/{$project->public_id}/tasks/export/pdf"
        )
        ->assertOk()
        ->assertHeader(
            'content-type',
            'application/pdf'
        );
});
