<?php

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Project;
use App\Models\ProjectExpense;
use App\Models\TaskWorkflowStatus;
use App\Models\Timesheet;
use App\Models\User;
use App\Services\Billing\PaymentAllocationService;
use App\Services\Finance\ProjectProfitabilityService;
use Database\Seeders\TaskWorkflowStatusSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(
        TaskWorkflowStatusSeeder::class
    );
});

it('calculates project revenue cost profit margin and collections', function (): void {
    $user =
        User::factory()->create();

    $project =
        Project::factory()->create([
            'currency' => 'LKR',

            'contract_value' => 100000,
        ]);

    $assignment =
        $project
            ->assignments()
            ->create([
                'user_id' => $user->id,

                'role' => 'Developer',

                'allocation_percent' => 100,

                'status' => 'active',

                'start_date' => today()
                    ->subMonth(),

                'billable' => true,

                'created_by' => $user->id,
            ]);

    $status =
        TaskWorkflowStatus::where(
            'slug',
            'done'
        )->firstOrFail();

    $task =
        $project
            ->tasks()
            ->create([
                'task_sequence' => 1,

                'task_number' => "{$project->project_number}-T0001",

                'workflow_status_id' => $status->id,

                'title' => 'M11 profitability task',

                'created_by' => $user->id,
            ]);

    $timesheet =
        Timesheet::create([
            'user_id' => $user->id,

            'week_start' => today()
                ->startOfWeek(),

            'week_end' => today()
                ->endOfWeek(),

            'status' => 'approved',
        ]);

    $timesheet
        ->entries()
        ->create([
            'project_id' => $project->id,

            'project_task_id' => $task->id,

            'project_assignment_id' => $assignment->id,

            'work_date' => today(),

            'hours' => 8,

            'billable' => true,

            'hourly_cost_rate_snapshot' => 2500,

            'internal_cost_amount' => 20000,

            'cost_currency' => 'LKR',

            'costed_at' => now(),
        ]);

    ProjectExpense::create([
        'expense_number' => 'EXP-M11-001',

        'project_id' => $project->id,

        'type' => 'hosting',

        'title' => 'Hosting',

        'expense_date' => today(),

        'currency' => 'LKR',

        'subtotal' => 10000,

        'tax_amount' => 0,

        'total_amount' => 10000,

        'status' => 'approved',
    ]);

    $invoice =
        Invoice::create([
            'invoice_number' => 'INV-M11-001',

            'client_id' => $project->client_id,

            'project_id' => $project->id,

            'status' => 'issued',

            'invoice_date' => today(),

            'due_date' => today()->addMonth(),

            'currency' => 'LKR',

            'billing_name' => $project->client->name,

            'total_amount' => 60000,

            'balance_due' => 60000,
        ]);

    $payment =
        Payment::create([
            'payment_number' => 'RCP-M11-001',

            'client_id' => $project->client_id,

            'payment_date' => today(),

            'currency' => 'LKR',

            'amount' => 30000,

            'allocated_amount' => 0,

            'unallocated_amount' => 30000,

            'method' => 'bank_transfer',
        ]);

    app(
        PaymentAllocationService::class
    )->allocate(
        $payment,
        $invoice,
        30000,
        $user
    );

    $result =
        app(
            ProjectProfitabilityService::class
        )->calculate(
            $project
        );

    expect(
        $result->contractedRevenue
    )->toBe(100000.0);

    expect(
        $result->invoicedRevenue
    )->toBe(60000.0);

    expect(
        $result->collectedCash
    )->toBe(30000.0);

    expect(
        $result->laborCost
    )->toBe(20000.0);

    expect(
        $result->nonLaborCost
    )->toBe(10000.0);

    expect(
        $result->actualCost
    )->toBe(30000.0);

    expect(
        $result->grossProfit
    )->toBe(70000.0);

    expect(
        $result->grossMarginPercent
    )->toBe(70.0);

    expect(
        $result->unbilledRevenue
    )->toBe(40000.0);

    expect(
        $result->outstandingReceivable
    )->toBe(30000.0);

    expect(
        $result->collectionPercent
    )->toBe(50.0);
});
