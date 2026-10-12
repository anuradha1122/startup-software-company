<?php

use App\Models\Client;
use App\Models\Estimate;
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

it('exports requirement srs pdf', function (): void {
    $admin = User::factory()->create();
    $admin->assignRole('Admin');

    $client = Client::create([
        'client_number' => 'CLI-PDF-03',

        'name' => 'PDF Client',

        'status' => 'active',

        'created_by' => $admin->id,
    ]);

    $requirement =
        Requirement::create([
            'requirement_number' => 'REQ-PDF-03',

            'client_id' => $client->id,

            'title' => 'PDF Requirement',

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

    $this
        ->actingAs($admin)
        ->get(
            "/admin/requirements/{$requirement->public_id}/versions/{$version->public_id}/srs-pdf"
        )
        ->assertOk()
        ->assertHeader(
            'content-type',
            'application/pdf'
        );
});

it('exports estimation excel and pdf', function (): void {
    $admin = User::factory()->create();
    $admin->assignRole('Admin');

    $client = Client::create([
        'client_number' => 'CLI-EXP-03',

        'name' => 'Export Client',

        'status' => 'active',

        'created_by' => $admin->id,
    ]);

    $requirement =
        Requirement::create([
            'requirement_number' => 'REQ-EXP-03',

            'client_id' => $client->id,

            'title' => 'Export Requirement',

            'status' => 'draft',

            'created_by' => $admin->id,
        ]);

    $version =
        $requirement
            ->versions()
            ->create([
                'version_number' => 1,

                'status' => 'draft',

                'created_by' => $admin->id,
            ]);

    $estimate =
        Estimate::create([
            'estimate_number' => 'EST-EXP-03',

            'requirement_version_id' => $version->id,

            'currency' => 'LKR',

            'status' => 'draft',

            'created_by' => $admin->id,
        ]);

    $this
        ->actingAs($admin)
        ->get(
            "/admin/estimates/{$estimate->public_id}/excel"
        )
        ->assertOk();

    $this
        ->actingAs($admin)
        ->get(
            "/admin/estimates/{$estimate->public_id}/pdf"
        )
        ->assertOk()
        ->assertHeader(
            'content-type',
            'application/pdf'
        );
});
