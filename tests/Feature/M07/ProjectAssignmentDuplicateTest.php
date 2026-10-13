<?php

use App\Models\Project;
use App\Models\User;
use App\Services\People\ProjectAssignmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('prevents duplicate assignment for same project user', function (): void {
    $admin =
        User::factory()->create();

    $person =
        User::factory()->create();

    $project =
        Project::factory()->create([
            'status' => 'active',
        ]);

    $service =
        app(
            ProjectAssignmentService::class
        );

    $service->create(
        $project,
        $person,
        [
            'role' => 'Developer',

            'allocation_percent' => 100,

            'status' => 'active',

            'billable' => true,
        ],
        $admin->id
    );

    expect(
        fn () => $service->create(
            $project,
            $person,
            [
                'role' => 'QA',

                'allocation_percent' => 50,

                'status' => 'active',

                'billable' => true,
            ],
            $admin->id
        )
    )->toThrow(
        RuntimeException::class
    );
});
