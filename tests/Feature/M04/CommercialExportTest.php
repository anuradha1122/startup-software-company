<?php

use App\Models\Client;
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

it('exports quotation pdf and excel', function (): void {
    $admin = User::factory()->create();
    $admin->assignRole('Admin');

    $client = Client::create([
        'client_number' => 'CLI-M04-04',
        'name' => 'Export Client',
        'status' => 'active',
        'created_by' => $admin->id,
    ]);

    $requirement =
        Requirement::create([
            'requirement_number' => 'REQ-M04-04',
            'client_id' => $client->id,
            'title' => 'Export Scope',
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
            'estimate_number' => 'EST-M04-04',
            'requirement_version_id' => $version->id,
            'status' => 'approved',
            'currency' => 'LKR',
            'estimated_revenue' => 100000,
            'created_by' => $admin->id,
        ]);

    $quotation =
        Quotation::create([
            'quotation_number' => 'QTN-M04-04',

            'client_id' => $client->id,

            'requirement_version_id' => $version->id,

            'estimate_id' => $estimate->id,

            'status' => 'approved',

            'currency' => 'LKR',

            'subtotal' => 100000,

            'total_amount' => 100000,

            'valid_until' => now()->addDays(30),

            'created_by' => $admin->id,
        ]);

    $this
        ->actingAs($admin)
        ->get(
            "/admin/quotations/{$quotation->public_id}/pdf"
        )
        ->assertOk()
        ->assertHeader(
            'content-type',
            'application/pdf'
        );

    $this
        ->actingAs($admin)
        ->get(
            "/admin/quotations/{$quotation->public_id}/excel"
        )
        ->assertOk();
});
