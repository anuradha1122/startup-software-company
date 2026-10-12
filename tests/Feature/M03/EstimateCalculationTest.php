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

it('calculates estimation revenue cost and margin', function (): void {
    $pm = User::factory()->create();
    $pm->assignRole(
        'Project Manager'
    );

    $client = Client::create([
        'client_number' => 'CLI-EST-01',

        'name' => 'Estimate Client',

        'status' => 'active',

        'created_by' => $pm->id,
    ]);

    $requirement =
        Requirement::create([
            'requirement_number' => 'REQ-EST-01',

            'client_id' => $client->id,

            'title' => 'Estimate Requirement',

            'status' => 'draft',

            'created_by' => $pm->id,
        ]);

    $version =
        $requirement
            ->versions()
            ->create([
                'version_number' => 1,

                'status' => 'draft',

                'created_by' => $pm->id,
            ]);

    $this
        ->actingAs($pm)
        ->post(
            "/admin/requirements/{$requirement->public_id}/versions/{$version->public_id}/estimates",
            [
                'currency' => 'LKR',

                'contingency_percent' => 10,

                'assumptions' => 'Normal working hours',

                'internal_notes' => null,

                'lines' => [
                    [
                        'module_name' => 'CRM',

                        'description' => 'CRM module',

                        'estimated_hours' => 100,

                        'billing_rate' => 5000,

                        'cost_rate' => 3000,
                    ],
                ],
            ]
        )
        ->assertRedirect();

    $estimate =
        Estimate::firstOrFail();

    expect(
        (float)
        $estimate->total_hours
    )->toBe(100.0);

    expect(
        (float)
        $estimate->estimated_revenue
    )->toBe(550000.0);

    expect(
        (float)
        $estimate->estimated_cost
    )->toBe(300000.0);

    expect(
        (float)
        $estimate->gross_margin
    )->toBe(250000.0);
});
