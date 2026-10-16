<?php

use App\Models\PayrollPeriod;
use App\Models\User;
use App\Services\Payroll\PayrollWorkflowService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

it('approves calculated payroll period', function (): void {
    $approver =
        User::factory()->create();

    $period =
        PayrollPeriod::create([
            'payroll_number' => 'PAY-2026-10',

            'name' => 'October 2026',

            'period_start' => '2026-10-01',

            'period_end' => '2026-10-31',

            'payment_date' => '2026-10-31',

            'status' => 'calculated',

            'calculated_at' => now(),
        ]);

    $result =
        app(
            PayrollWorkflowService::class
        )->approve(
            $period,
            $approver
        );

    expect(
        $result->status
    )->toBe('approved');

    expect(
        $result->approved_by
    )->toBe(
        $approver->id
    );

    expect(
        $result->approved_at
    )->not->toBeNull();
});

it('does not approve payroll before calculation', function (): void {
    $approver =
        User::factory()->create();

    $period =
        PayrollPeriod::create([
            'payroll_number' => 'PAY-2026-11',

            'name' => 'November 2026',

            'period_start' => '2026-11-01',

            'period_end' => '2026-11-30',

            'payment_date' => '2026-11-30',

            'status' => 'draft',
        ]);

    expect(
        fn () => app(
            PayrollWorkflowService::class
        )->approve(
            $period,
            $approver
        )
    )->toThrow(
        ValidationException::class
    );

    expect(
        $period
            ->fresh()
            ->status
    )->toBe('draft');
});
