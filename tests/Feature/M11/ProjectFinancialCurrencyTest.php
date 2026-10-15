<?php

use App\Models\Project;
use App\Models\ProjectExpense;
use App\Services\Finance\ProjectProfitabilityService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('detects financial source currency mismatch', function (): void {
    $project =
        Project::factory()->create([
            'currency' => 'LKR',

            'contract_value' => 100000,
        ]);

    ProjectExpense::create([
        'expense_number' => 'EXP-M11-USD',

        'project_id' => $project->id,

        'type' => 'software',

        'title' => 'USD software',

        'expense_date' => today(),

        'currency' => 'USD',

        'subtotal' => 100,

        'tax_amount' => 0,

        'total_amount' => 100,

        'status' => 'approved',
    ]);

    $result =
        app(
            ProjectProfitabilityService::class
        )->calculate(
            $project
        );

    expect(
        $result
            ->currencyMismatchCount()
    )->toBe(1);

    expect(
        $result->nonLaborCost
    )->toBe(0.0);
});
