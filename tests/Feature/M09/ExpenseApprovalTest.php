<?php

use App\Models\Project;
use App\Models\ProjectExpense;
use App\Models\User;
use App\Notifications\ProjectExpenseApprovedNotification;
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

it('approves submitted project expense', function (): void {
    $creator =
        User::factory()->create();

    $reviewer =
        User::factory()->create();

    $reviewer->assignRole('Admin');

    $project =
        Project::factory()->create([
            'status' => 'active',
        ]);

    $expense =
        ProjectExpense::create([
            'expense_number' => 'EXP-000001',

            'project_id' => $project->id,

            'type' => 'supplier',

            'title' => 'Supplier service',

            'expense_date' => today(),

            'currency' => 'LKR',

            'subtotal' => 50000,

            'tax_amount' => 0,

            'total_amount' => 50000,

            'status' => 'submitted',

            'submitted_at' => now(),

            'created_by' => $creator->id,
        ]);

    $this
        ->actingAs($reviewer)
        ->post(
            "/admin/expenses/{$expense->public_id}/review",
            [
                'decision' => 'approved',

                'review_notes' => 'Approved.',
            ]
        )
        ->assertRedirect();

    $expense->refresh();

    expect(
        $expense->status
    )->toBe('approved');

    expect(
        $expense->reviewed_by
    )->toBe(
        $reviewer->id
    );

    Notification::assertSentTo(
        $creator,
        ProjectExpenseApprovedNotification::class
    );
});
