<?php

use App\Models\Project;
use App\Models\ProjectExpense;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Storage::fake('local');

    $this->seed(
        RolePermissionSeeder::class
    );
});

it('stores expense attachment privately', function (): void {
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

            'type' => 'software',

            'title' => 'Subscription',

            'expense_date' => today(),

            'currency' => 'LKR',

            'subtotal' => 10000,

            'tax_amount' => 0,

            'total_amount' => 10000,

            'status' => 'draft',

            'created_by' => $admin->id,
        ]);

    $file =
        UploadedFile::fake()
            ->create(
                'invoice.pdf',
                200,
                'application/pdf'
            );

    $this
        ->actingAs($admin)
        ->post(
            "/admin/expenses/{$expense->public_id}/attachments",
            [
                'type' => 'supplier_invoice',

                'title' => 'Supplier Invoice',

                'file' => $file,
            ]
        )
        ->assertRedirect();

    $attachment =
        $expense
            ->attachments()
            ->firstOrFail();

    expect(
        $attachment->disk
    )->toBe('local');

    Storage::disk('local')
        ->assertExists(
            $attachment->path
        );
});
