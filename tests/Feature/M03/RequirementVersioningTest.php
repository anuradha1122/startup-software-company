<?php

use App\Models\Client;
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

it('creates requirement with initial version', function (): void {
    $pm = User::factory()->create();
    $pm->assignRole(
        'Project Manager'
    );

    $client = Client::create([
        'client_number' => 'CLI-M03-01',

        'name' => 'Requirements Client',

        'status' => 'active',

        'created_by' => $pm->id,
    ]);

    $this
        ->actingAs($pm)
        ->post(
            '/admin/requirements',
            [
                'client_id' => $client->id,

                'title' => 'ERP Requirements',

                'owner_id' => $pm->id,

                'objective' => 'Build ERP',

                'scope_in' => 'CRM and Finance',

                'scope_out' => 'Payroll',

                'functional_requirements' => [
                    'Manage clients',
                    'Create invoices',
                ],

                'non_functional_requirements' => [
                    'Responsive UI',
                ],

                'assumptions' => [
                    'Client provides data',
                ],

                'constraints' => [
                    'Laravel stack',
                ],

                'acceptance_criteria' => [
                    'UAT accepted',
                ],

                'client_notes' => null,

                'internal_notes' => 'Internal only',
            ]
        )
        ->assertRedirect();

    $requirement =
        Requirement::firstOrFail();

    expect(
        $requirement
            ->requirement_number
    )->toStartWith('REQ-');

    expect(
        $requirement
            ->versions()
            ->count()
    )->toBe(1);

    expect(
        $requirement
            ->versions()
            ->first()
            ->version_number
    )->toBe(1);
});
