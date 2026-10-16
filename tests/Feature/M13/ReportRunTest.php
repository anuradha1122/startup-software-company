<?php

use App\Models\ReportRun;
use App\Models\User;
use App\Services\Reports\ReportRunService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('records completed report run metadata', function (): void {
    $user =
        User::factory()->create();

    $run =
        app(
            ReportRunService::class
        )->completed(
            type: 'project_operations',
            format: 'excel',
            user: $user,
            downloadName: 'project-operations.xlsx',
            rowCount: 12,
            filters: [
                'status' => 'active',
            ]
        );

    expect(
        $run
    )->toBeInstanceOf(
        ReportRun::class
    );

    expect(
        $run->status
    )->toBe('completed');

    expect(
        $run->row_count
    )->toBe(12);

    expect(
        $run->filters
    )->toBe([
        'status' => 'active',
    ]);
});
