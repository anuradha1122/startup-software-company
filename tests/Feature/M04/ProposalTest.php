<?php

use App\Models\Client;
use App\Models\Estimate;
use App\Models\Proposal;
use App\Models\Requirement;
use App\Models\User;
use Database\Seeders\CompanySettingSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed([
        RolePermissionSeeder::class,
        CompanySettingSeeder::class,
    ]);
});

it('creates a proposal only from approved requirement and estimate', function (): void {
    $admin = User::factory()->create();
    $admin->assignRole('Admin');

    $client = Client::create([
        'client_number' => 'CLI-M04-01',
        'name' => 'M04 Client',
        'status' => 'active',
        'created_by' => $admin->id,
    ]);

    $requirement =
        Requirement::create([
            'requirement_number' => 'REQ-M04-01',
            'client_id' => $client->id,
            'title' => 'Commercial Scope',
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
            'estimate_number' => 'EST-M04-01',

            'requirement_version_id' => $version->id,

            'status' => 'approved',

            'currency' => 'LKR',

            'estimated_revenue' => 500000,

            'created_by' => $admin->id,
        ]);

    $this
        ->actingAs($admin)
        ->post(
            '/admin/proposals',
            [
                'requirement_version_id' => $version->id,

                'estimate_id' => $estimate->id,

                'title' => 'ERP Proposal',

                'executive_summary' => 'Summary',

                'solution_overview' => 'Solution',

                'delivery_approach' => 'Agile',

                'commercial_notes' => null,

                'valid_until' => now()
                    ->addDays(30)
                    ->format('Y-m-d'),
            ]
        )
        ->assertRedirect();

    expect(
        Proposal::first()
            ->proposal_number
    )->toStartWith('PRP-');
});
