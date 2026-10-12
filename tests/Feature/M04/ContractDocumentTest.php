<?php

use App\Models\Client;
use App\Models\Contract;
use App\Models\Estimate;
use App\Models\Quotation;
use App\Models\Requirement;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Storage::fake('local');

    $this->seed(
        RolePermissionSeeder::class
    );
});

it('stores signed contract privately', function (): void {
    $admin = User::factory()->create();
    $admin->assignRole('Admin');

    $client = Client::create([
        'client_number' => 'CLI-M04-DOC',
        'name' => 'Contract Doc Client',
        'status' => 'active',
        'created_by' => $admin->id,
    ]);

    $requirement =
        Requirement::create([
            'requirement_number' => 'REQ-M04-DOC',
            'client_id' => $client->id,
            'title' => 'Contract Doc Scope',
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
            'estimate_number' => 'EST-M04-DOC',
            'requirement_version_id' => $version->id,
            'status' => 'approved',
            'currency' => 'LKR',
            'created_by' => $admin->id,
        ]);

    $quotation =
        Quotation::create([
            'quotation_number' => 'QTN-M04-DOC',
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
            'contract_number' => 'CON-M04-DOC',
            'quotation_id' => $quotation->id,
            'client_id' => $client->id,
            'requirement_version_id' => $version->id,
            'estimate_id' => $estimate->id,
            'status' => 'issued',
            'title' => 'Contract',
            'currency' => 'LKR',
            'contract_value' => 100000,
            'created_by' => $admin->id,
        ]);

    $this
        ->actingAs($admin)
        ->post(
            "/admin/contracts/{$contract->public_id}/documents",
            [
                'type' => 'signed_contract',

                'title' => 'Signed Contract',

                'file' => UploadedFile::fake()
                    ->create(
                        'signed.pdf',
                        100,
                        'application/pdf'
                    ),
            ]
        )
        ->assertRedirect();

    $document =
        $contract
            ->documents()
            ->firstOrFail();

    Storage::disk('local')
        ->assertExists(
            $document->path
        );
});
