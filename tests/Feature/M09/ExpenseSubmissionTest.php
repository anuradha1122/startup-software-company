<?php

use App\Models\Project;
use App\Models\ProjectExpense;
use App\Models\User;
use App\Notifications\ProjectExpenseSubmittedNotification;
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

it('submits expense with supporting evidence', function (): void {
    $admin =
        User::factory()->create();

    $admin->assignRole('Admin');

    $reviewer =
        User::factory()->create();

    $reviewer->assignRole(
        'Project Manager'
    );

    $project =
        Project::factory()->create([
            'status' => 'active',
        ]);

    $expense =
        ProjectExpense::create([
            'expense_number' => 'EXP-000001',

            'project_id' => $project->id,

            'type' => 'hosting',

            'title' => 'Hosting',

            'expense_date' => today(),

            'currency' => 'LKR',

            'subtotal' => 10000,

            'tax_amount' => 0,

            'total_amount' => 10000,

            'status' => 'draft',

            'created_by' => $admin->id,
        ]);

    $expense
        ->attachments()
        ->create([
            'type' => 'receipt',

            'title' => 'Receipt',

            'original_name' => 'receipt.pdf',

            'disk' => 'local',

            'path' => 'test/receipt.pdf',

            'mime_type' => 'application/pdf',

            'size' => 100,

            'uploaded_by' => $admin->id,
        ]);

    $this
        ->actingAs($admin)
        ->post(
            "/admin/expenses/{$expense->public_id}/submit"
        )
        ->assertRedirect();

    expect(
        $expense
            ->fresh()
            ->status
    )->toBe('submitted');

    Notification::assertSentTo(
        $reviewer,
        ProjectExpenseSubmittedNotification::class
    );
});

it('does not submit expense without supporting evidence', function (): void {
    $admin =
        User::factory()->create();

    $admin->assignRole('Admin');

    $project =
        Project::factory()->create([
            'status' => 'active',
        ]);

    $expense =
        ProjectExpense::create([
            'expense_number' => 'EXP-000001',

            'project_id' => $project->id,

            'type' => 'other',

            'title' => 'Missing receipt',

            'expense_date' => today(),

            'currency' => 'LKR',

            'subtotal' => 1000,

            'tax_amount' => 0,

            'total_amount' => 1000,

            'status' => 'draft',

            'created_by' => $admin->id,
        ]);

    $this
        ->withoutExceptionHandling()
        ->actingAs($admin);

    expect(
        fn () => $this->post(
            "/admin/expenses/{$expense->public_id}/submit"
        )
    )->toThrow(
        RuntimeException::class,
        'supporting attachment'
    );
});
