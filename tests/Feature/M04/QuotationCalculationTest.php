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

it('calculates quotation totals from approved estimate', function (): void {
    $admin = User::factory()->create();
    $admin->assignRole('Admin');

    $client = Client::create([
        'client_number' => 'CLI-M04-02',
        'name' => 'Quotation Client',
        'status' => 'active',
        'created_by' => $admin->id,
    ]);

    $requirement =
        Requirement::create([
            'requirement_number' => 'REQ-M04-02',
            'client_id' => $client->id,
            'title' => 'Quotation Scope',
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
            'estimate_number' => 'EST-M04-02',
            'requirement_version_id' => $version->id,
            'status' => 'approved',
            'currency' => 'LKR',
            'estimated_revenue' => 100000,
            'created_by' => $admin->id,
        ]);

    $this
        ->actingAs($admin)
        ->post(
            '/admin/quotations',
            [
                'proposal_id' => null,

                'requirement_version_id' => $version->id,

                'estimate_id' => $estimate->id,

                'currency' => 'LKR',

                'discount_percent' => 10,

                'tax_percent' => 18,

                'payment_terms_days' => 30,

                'payment_schedule' => null,

                'terms_and_conditions' => 'Standard terms',

                'notes' => null,

                'valid_until' => now()
                    ->addDays(30)
                    ->format('Y-m-d'),
            ]
        )
        ->assertRedirect();

    $quotation =
        Quotation::firstOrFail();

    expect(
        (float) $quotation->subtotal
    )->toBe(100000.0);

    expect(
        (float)
        $quotation->discount_amount
    )->toBe(10000.0);

    expect(
        (float)
        $quotation->tax_amount
    )->toBe(16200.0);

    expect(
        (float)
        $quotation->total_amount
    )->toBe(106200.0);
});
