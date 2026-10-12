<?php

use App\Models\Client;
use App\Models\Lead;
use App\Models\LeadStage;
use App\Models\User;
use Database\Seeders\LeadStageSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed([
        RolePermissionSeeder::class,
        LeadStageSeeder::class,
    ]);
});

it('converts a lead to a client without duplicating contacts', function (): void {
    $sales = User::factory()->create();
    $sales->assignRole('Sales');

    $stage = LeadStage::where(
        'slug',
        'qualified'
    )->firstOrFail();

    $lead = Lead::create([
        'lead_number' => 'LEAD-TEST02',
        'title' => 'Website Project',
        'company_name' => 'Example Ltd',
        'stage_id' => $stage->id,
        'probability' => 50,
        'priority' => 'high',
        'currency' => 'LKR',
        'status' => 'open',
        'owner_id' => $sales->id,
        'created_by' => $sales->id,
    ]);

    $lead->contacts()->create([
        'name' => 'Jane Client',
        'email' => 'jane@example.com',
        'phone' => '0770000000',
        'is_primary' => true,
    ]);

    $this
        ->actingAs($sales)
        ->post(
            "/admin/leads/{$lead->public_id}/convert"
        )
        ->assertRedirect();

    $lead->refresh();

    expect($lead->status)
        ->toBe('converted');

    expect(
        Client::where(
            'source_lead_id',
            $lead->id
        )->count()
    )->toBe(1);

    $client = Client::where(
        'source_lead_id',
        $lead->id
    )->firstOrFail();

    expect(
        $client->contacts()->count()
    )->toBe(1);
});
