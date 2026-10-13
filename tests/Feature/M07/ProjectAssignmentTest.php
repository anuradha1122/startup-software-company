<?php

use App\Models\Project;
use App\Models\User;
use App\Notifications\ProjectAssignmentCreatedNotification;
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

it('adds active user to active project team', function (): void {
    $admin =
        User::factory()->create();

    $admin->assignRole('Admin');

    $person =
        User::factory()->create([
            'status' => 'active',
        ]);

    $project =
        Project::factory()->create([
            'status' => 'active',
        ]);

    $this
        ->actingAs($admin)
        ->post(
            "/admin/projects/{$project->public_id}/team",
            [
                'user_id' => $person->id,

                'role' => 'Developer',

                'allocation_percent' => 75,

                'status' => 'active',

                'start_date' => today()
                    ->format('Y-m-d'),

                'end_date' => today()
                    ->addMonths(3)
                    ->format('Y-m-d'),

                'billable' => true,

                'responsibilities' => 'Backend implementation',

                'notes' => null,
            ]
        )
        ->assertRedirect();

    $assignment =
        $project
            ->assignments()
            ->where(
                'user_id',
                $person->id
            )
            ->firstOrFail();

    expect(
        $assignment->role
    )->toBe('Developer');

    expect(
        $assignment
            ->allocation_percent
    )->toBe(75);

    Notification::assertSentTo(
        $person,
        ProjectAssignmentCreatedNotification::class
    );
});
