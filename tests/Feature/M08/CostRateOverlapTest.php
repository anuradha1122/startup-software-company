<?php

use App\Models\ResourceCostRate;
use App\Models\User;
use App\Services\Timesheets\ResourceCostRateService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('rejects overlapping internal cost rate periods', function (): void {
    $user = User::factory()->create();

    ResourceCostRate::create([
        'user_id' => $user->id,

        'currency' => 'LKR',

        'hourly_cost_rate' => 2500,

        'effective_from' => '2026-01-01',

        'effective_to' => '2026-12-31',

        'active' => true,
    ]);

    expect(
        fn () => app(
            ResourceCostRateService::class
        )->assertNoOverlap(
            $user->id,
            Carbon::parse(
                '2026-06-01'
            ),
            Carbon::parse(
                '2027-01-31'
            )
        )
    )->toThrow(
        RuntimeException::class,
        'overlaps'
    );
});

it('allows a non overlapping internal cost rate period', function (): void {
    $user = User::factory()->create();

    ResourceCostRate::create([
        'user_id' => $user->id,

        'currency' => 'LKR',

        'hourly_cost_rate' => 2500,

        'effective_from' => '2026-01-01',

        'effective_to' => '2026-06-30',

        'active' => true,
    ]);

    app(
        ResourceCostRateService::class
    )->assertNoOverlap(
        $user->id,
        Carbon::parse(
            '2026-07-01'
        ),
        Carbon::parse(
            '2026-12-31'
        )
    );

    expect(true)->toBeTrue();
});
