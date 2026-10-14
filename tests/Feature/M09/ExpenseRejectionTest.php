<?php

use App\Models\Project;
use App\Models\ProjectExpense;
use App\Models\User;
use App\Notifications\ProjectExpenseRejectedNotification;
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

it('rejects expense and makes it editable again', function (): void {
    $creator =
        User::factory()->create();

    $creator->assignRole(
        'Project Manager'
    );

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

            'type' => 'travel',

            'title' => 'Travel cost',

            'expense_date' => today(),

            'currency' => 'LKR',

            'subtotal' => 10000,

            'tax_amount' => 0,

            'total_amount' => 10000,

            'status' => 'submitted',

            'submitted_at' => now(),

            'created_by' => $creator->id,
        ]);

    $this
        ->actingAs($reviewer)
        ->post(
            "/admin/expenses/{$expense->public_id}/review",
            [
                'decision' => 'rejected',

                'review_notes' => 'Attach the correct receipt.',
            ]
        )
        ->assertRedirect();

    $expense->refresh();

    expect(
        $expense->status
    )->toBe('rejected');

    expect(
        $expense->isEditable()
    )->toBeTrue();

    expect(
        $expense->review_notes
    )->toBe(
        'Attach the correct receipt.'
    );

    Notification::assertSentTo(
        $creator,
        ProjectExpenseRejectedNotification::class
    );
});
