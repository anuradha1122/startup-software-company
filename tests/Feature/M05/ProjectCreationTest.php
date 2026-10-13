<?php

use App\Models\Client;
use App\Models\Contract;
use App\Models\Estimate;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\Requirement;
use App\Models\User;
use Database\Seeders\CompanySettingSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Notification::fake();

    $this->seed([
        RolePermissionSeeder::class,
        CompanySettingSeeder::class,
    ]);
});

it('creates project from signed contract and preserves baseline', function (): void {
    $admin =
        User::factory()->create();

    $admin->assignRole('Admin');

    $pm =
        User::factory()->create();

    $pm->assignRole(
        'Project Manager'
    );

    $client =
        Client::create([
            'client_number' => 'CLI-M05-01',

            'name' => 'Project Client',

            'status' => 'active',

            'created_by' => $admin->id,
        ]);

    $requirement =
        Requirement::create([
            'requirement_number' => 'REQ-M05-01',

            'client_id' => $client->id,

            'title' => 'Project Scope',

            'status' => 'approved',

            'created_by' => $admin->id,
        ]);

    $version =
        $requirement
            ->versions()
            ->create([
                'version_number' => 1,
                'status' => 'approved',
                'created_by' => $admin->id,
            ]);

    $estimate =
        Estimate::create([
            'estimate_number' => 'EST-M05-01',

            'requirement_version_id' => $version->id,

            'status' => 'approved',

            'currency' => 'LKR',

            'created_by' => $admin->id,
        ]);

    $quotation =
        Quotation::create([
            'quotation_number' => 'QTN-M05-01',

            'client_id' => $client->id,

            'requirement_version_id' => $version->id,

            'estimate_id' => $estimate->id,

            'status' => 'accepted',

            'currency' => 'LKR',

            'total_amount' => 500000,

            'valid_until' => now()->addDays(30),

            'created_by' => $admin->id,
        ]);

    $contract =
        Contract::create([
            'contract_number' => 'CON-M05-01',

            'quotation_id' => $quotation->id,

            'client_id' => $client->id,

            'requirement_version_id' => $version->id,

            'estimate_id' => $estimate->id,

            'status' => 'signed',

            'title' => 'Software Contract',

            'currency' => 'LKR',

            'contract_value' => 500000,

            'created_by' => $admin->id,

            'signed_at' => now(),
        ]);

    $this
        ->actingAs($admin)
        ->post(
            '/admin/projects',
            [
                'contract_id' => $contract->id,

                'name' => 'ERP Project',

                'methodology' => 'Agile',

                'description' => null,

                'project_manager_id' => $pm->id,

                'client_sponsor_contact_id' => null,

                'planned_start_date' => now()
                    ->format('Y-m-d'),

                'planned_end_date' => now()
                    ->addMonths(6)
                    ->format('Y-m-d'),
            ]
        )
        ->assertRedirect();

    $project =
        Project::firstOrFail();

    expect(
        $project->project_number
    )->toStartWith('PRJ-');

    expect(
        $project->contract_id
    )->toBe($contract->id);

    expect(
        $project->client_id
    )->toBe($client->id);

    expect(
        $project->contract_value
    )->toBe('500000.00');

    expect(
        $project->charters()->count()
    )->toBe(1);
});
