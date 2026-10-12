<?php

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

it('allows sales to move a lead between pipeline stages', function (): void {
    $sales = User::factory()->create();
    $sales->assignRole('Sales');

    $newStage = LeadStage::where(
        'slug',
        'new'
    )->firstOrFail();

    $qualifiedStage =
        LeadStage::where(
            'slug',
            'qualified'
        )->firstOrFail();

    $lead = Lead::create([
        'lead_number' => 'LEAD-TEST01',
        'title' => 'Test Lead',
        'stage_id' => $newStage->id,
        'probability' => $newStage->probability,
        'currency' => 'LKR',
        'priority' => 'medium',
        'status' => 'open',
        'created_by' => $sales->id,
    ]);

    $this
        ->actingAs($sales)
        ->patch(
            "/admin/leads/{$lead->public_id}/stage",
            [
                'stage_id' => $qualifiedStage->id,
            ]
        )
        ->assertRedirect();

    expect(
        $lead->refresh()->stage_id
    )->toBe($qualifiedStage->id);

    expect(
        $lead->probability
    )->toBe(
        $qualifiedStage->probability
    );
});
