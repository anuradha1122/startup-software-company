<?php

use App\Models\Project;
use App\Services\Projects\ProjectRiskService;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(
        RolePermissionSeeder::class
    );
});

it('calculates project risk score', function (): void {
    $project =
        Project::factory()->create();

    $risk =
        app(
            ProjectRiskService::class
        )->create(
            $project,
            [
                'title' => 'Delivery delay',

                'description' => null,

                'category' => 'Schedule',

                'likelihood' => 4,

                'impact' => 5,

                'status' => 'open',

                'owner_id' => null,

                'mitigation' => 'Weekly monitoring',

                'contingency' => null,

                'review_date' => null,
            ]
        );

    expect(
        $risk->score
    )->toBe(20);

    expect(
        $risk->risk_number
    )->toBe('RISK-001');
});
