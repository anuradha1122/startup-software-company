<?php

use App\Models\ChangeRequest;
use App\Models\Project;
use App\Models\SupportTicket;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(
        RolePermissionSeeder::class
    );
});

it('exports support tickets and change requests', function (): void {
    $admin =
        User::factory()->create();

    $admin->assignRole('Admin');

    $project =
        Project::factory()->create();

    SupportTicket::create([
        'ticket_number' => 'SUP-000001',

        'client_id' => $project->client_id,

        'project_id' => $project->id,

        'subject' => 'Issue',

        'description' => 'Issue',

        'priority' => 'medium',

        'severity' => 'normal',

        'status' => 'open',
    ]);

    ChangeRequest::create([
        'change_request_number' => 'CR-000001',

        'client_id' => $project->client_id,

        'project_id' => $project->id,

        'title' => 'Change',

        'description' => 'Change',

        'priority' => 'medium',

        'status' => 'submitted',

        'submitted_at' => now(),
    ]);

    $this
        ->actingAs($admin)
        ->get(
            '/admin/support/export/excel'
        )
        ->assertOk();

    $this
        ->actingAs($admin)
        ->get(
            '/admin/change-requests/export/excel'
        )
        ->assertOk();
});
