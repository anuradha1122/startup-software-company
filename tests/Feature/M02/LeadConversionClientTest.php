<?php

use App\Models\Lead;
use App\Models\LeadStage;
use App\Models\User;
use Database\Seeders\CompanySettingSeeder;
use Database\Seeders\LeadStageSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Notification::fake();

    $this->seed([
        RolePermissionSeeder::class,
        CompanySettingSeeder::class,
        LeadStageSeeder::class,
    ]);
});

it('assigns a client number when converting a lead', function (): void {
    $sales = User::factory()->create();
    $sales->assignRole('Sales');

    $stage = LeadStage::where(
        'slug',
        'qualified'
    )->firstOrFail();

    $lead = Lead::create([
        'lead_number' => 'LEAD-M02-01',
        'title' => 'Converted Customer',
        'company_name' => 'Converted Ltd',
        'stage_id' => $stage->id,
        'owner_id' => $sales->id,
        'probability' => 50,
        'priority' => 'medium',
        'currency' => 'LKR',
        'status' => 'open',
        'created_by' => $sales->id,
    ]);

    $this
        ->actingAs($sales)
        ->post(
            "/admin/leads/{$lead->public_id}/convert"
        )
        ->assertRedirect();

    $client = $lead
        ->refresh()
        ->convertedClient;

    expect($client)
        ->not->toBeNull();

    expect(
        $client->client_number
    )->toStartWith('CLI-');
});
