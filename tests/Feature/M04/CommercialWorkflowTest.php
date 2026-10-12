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
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Notification::fake();

    $this->seed([
        RolePermissionSeeder::class,
        CompanySettingSeeder::class,
    ]);
});

it('allows accepted quotation to become contract', function (): void {
    $admin = User::factory()->create();
    $admin->assignRole('Admin');

    $client = Client::create([
        'client_number' => 'CLI-M04-03',
        'name' => 'Contract Client',
        'status' => 'active',
        'created_by' => $admin->id,
    ]);

    $requirement =
        Requirement::create([
            'requirement_number' => 'REQ-M04-03',
            'client_id' => $client->id,
            'title' => 'Contract Scope',
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
            'estimate_number' => 'EST-M04-03',
            'requirement_version_id' => $version->id,
            'status' => 'approved',
            'currency' => 'LKR',
            'estimated_revenue' => 250000,
            'created_by' => $admin->id,
        ]);

    $quotation =
        Quotation::create([
            'quotation_number' => 'QTN-M04-01',

            'client_id' => $client->id,

            'requirement_version_id' => $version->id,

            'estimate_id' => $estimate->id,

            'status' => 'accepted',

            'currency' => 'LKR',

            'subtotal' => 250000,

            'total_amount' => 250000,

            'valid_until' => now()->addDays(30),

            'created_by' => $admin->id,
        ]);

    $this
        ->actingAs($admin)
        ->post(
            '/admin/contracts',
            [
                'quotation_id' => $quotation->id,

                'title' => 'Software Contract',

                'start_date' => now()
                    ->format('Y-m-d'),

                'end_date' => now()
                    ->addMonths(6)
                    ->format('Y-m-d'),

                'payment_terms_days' => 30,

                'payment_schedule' => '50% advance',

                'special_terms' => null,

                'termination_terms' => null,
            ]
        )
        ->assertRedirect();

    $contract =
        Contract::firstOrFail();

    expect(
        $contract->contract_value
    )->toBe('250000.00');

    expect(
        $contract->quotation_id
    )->toBe(
        $quotation->id
    );
});
