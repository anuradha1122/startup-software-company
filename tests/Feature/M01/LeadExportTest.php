<?php

use App\Models\User;
use Database\Seeders\CompanySettingSeeder;
use Database\Seeders\LeadStageSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed([
        RolePermissionSeeder::class,
        CompanySettingSeeder::class,
        LeadStageSeeder::class,
    ]);
});

it('exports leads to excel', function (): void {
    $admin = User::factory()->create();
    $admin->assignRole('Admin');

    $this
        ->actingAs($admin)
        ->get(
            '/admin/leads/export/excel'
        )
        ->assertOk();
});

it('exports pipeline to pdf', function (): void {
    $admin = User::factory()->create();
    $admin->assignRole('Admin');

    $this
        ->actingAs($admin)
        ->get(
            '/admin/leads/export/pipeline-pdf'
        )
        ->assertOk()
        ->assertHeader(
            'content-type',
            'application/pdf'
        );
});
