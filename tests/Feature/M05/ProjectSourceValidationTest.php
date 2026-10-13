<?php

use App\Models\Client;
use App\Models\Contract;
use App\Models\Estimate;
use App\Models\Quotation;
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

it('rejects project creation from unsigned contract', function (): void {
    $admin =
        User::factory()->create();

    $admin->assignRole('Admin');

    $pm =
        User::factory()->create();

    $client =
        Client::create([
            'client_number' => 'CLI-M05-02',

            'name' => 'Unsigned Client',

            'status' => 'active',

            'created_by' => $admin->id,
        ]);

    $requirement =
        Requirement::create([
            'requirement_number' => 'REQ-M05-02',

            'client_id' => $client->id,

            'title' => 'Scope',

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
            'estimate_number' => 'EST-M05-02',

            'requirement_version_id' => $version->id,

            'status' => 'approved',

            'currency' => 'LKR',

            'created_by' => $admin->id,
        ]);

    $quotation =
        Quotation::create([
            'quotation_number' => 'QTN-M05-02',

            'client_id' => $client->id,

            'requirement_version_id' => $version->id,

            'estimate_id' => $estimate->id,

            'status' => 'accepted',

            'currency' => 'LKR',

            'total_amount' => 100000,

            'valid_until' => now()->addDays(30),

            'created_by' => $admin->id,
        ]);

    $contract =
        Contract::create([
            'contract_number' => 'CON-M05-02',

            'quotation_id' => $quotation->id,

            'client_id' => $client->id,

            'requirement_version_id' => $version->id,

            'estimate_id' => $estimate->id,

            'status' => 'issued',

            'title' => 'Unsigned Contract',

            'currency' => 'LKR',

            'contract_value' => 100000,

            'created_by' => $admin->id,
        ]);

    $this
        ->withoutExceptionHandling()
        ->actingAs($admin);

    expect(
        fn () => $this->post(
            '/admin/projects',
            [
                'contract_id' => $contract->id,

                'name' => 'Invalid Project',

                'project_manager_id' => $pm->id,
            ]
        )
    )->toThrow(
        RuntimeException::class
    );
});
