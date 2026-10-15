<?php

use App\Models\Project;
use App\Models\ProjectFinancialSnapshot;
use App\Models\User;
use App\Services\Finance\ProjectFinancialSnapshotService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('captures one financial snapshot per project per day', function (): void {
    $user =
        User::factory()->create();

    $project =
        Project::factory()->create([
            'currency' => 'LKR',

            'contract_value' => 100000,
        ]);

    $service =
        app(
            ProjectFinancialSnapshotService::class
        );

    $service->capture(
        $project,
        $user
    );

    $service->capture(
        $project,
        $user
    );

    expect(
        ProjectFinancialSnapshot::where(
            'project_id',
            $project->id
        )->count()
    )->toBe(1);

    $snapshot =
        ProjectFinancialSnapshot::firstOrFail();

    expect(
        $snapshot->currency
    )->toBe('LKR');

    expect(
        (float) $snapshot
            ->contracted_revenue
    )->toBe(100000.0);
});
