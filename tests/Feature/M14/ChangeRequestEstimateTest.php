<?php

use App\Models\ChangeRequest;
use App\Models\Project;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(
        RolePermissionSeeder::class
    );
});

it('stores one estimate for change request', function (): void {
    $admin =
        User::factory()->create();

    $admin->assignRole('Admin');

    $project =
        Project::factory()->create();

    $changeRequest =
        ChangeRequest::create([
            'change_request_number' => 'CR-000001',

            'client_id' => $project->client_id,

            'project_id' => $project->id,

            'title' => 'New feature',

            'description' => 'Feature',

            'priority' => 'medium',

            'status' => 'submitted',

            'submitted_at' => now(),
        ]);

    $this
        ->actingAs($admin)
        ->post(
            "/admin/change-requests/{$changeRequest->public_id}/estimate",
            [
                'currency' => 'LKR',

                'client_amount' => 50000,

                'internal_estimated_cost' => 30000,

                'estimated_hours' => 20,

                'estimated_days' => 3,

                'client_notes' => 'Estimated delivery 3 days.',

                'internal_notes' => 'Internal cost basis.',
            ]
        )
        ->assertRedirect();

    $estimate =
        $changeRequest
            ->estimate()
            ->firstOrFail();

    expect(
        (float) $estimate
            ->client_amount
    )->toBe(50000.0);

    expect(
        (float) $estimate
            ->internal_estimated_cost
    )->toBe(30000.0);

    expect(
        $changeRequest
            ->fresh()
            ->status
    )->toBe('under_review');
});
