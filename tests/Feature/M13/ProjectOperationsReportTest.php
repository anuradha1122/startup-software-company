<?php

use App\Models\Project;
use App\Services\Reports\ProjectOperationsReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('builds a cross module project operations row', function (): void {
    $project =
        Project::factory()->create([
            'status' => 'active',

            'currency' => 'LKR',

            'contract_value' => 100000,
        ]);

    $rows =
        app(
            ProjectOperationsReportService::class
        )->rows();

    expect($rows)
        ->toHaveCount(1);

    $row =
        $rows->first();

    expect(
        $row['project_number']
    )->toBe(
        $project->project_number
    );

    expect(
        $row['currency']
    )->toBe('LKR');

    expect(
        $row['contracted_revenue']
    )->toBe(100000.0);

    expect(
        $row['active_team_count']
    )->toBe(0);

    expect(
        $row['open_task_count']
    )->toBe(0);

    expect(
        $row[
            'pending_client_approval_count'
        ]
    )->toBe(0);
});
