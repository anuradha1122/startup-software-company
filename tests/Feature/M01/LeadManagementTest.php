<?php

use App\Models\Lead;
use App\Models\LeadStage;
use App\Models\User;
use App\Notifications\LeadAssignedNotification;
use Database\Seeders\CompanySettingSeeder;
use Database\Seeders\LeadSourceSeeder;
use Database\Seeders\LeadStageSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed([
        RolePermissionSeeder::class,
        CompanySettingSeeder::class,
        LeadStageSeeder::class,
        LeadSourceSeeder::class,
    ]);
});

it('allows sales to create a lead', function (): void {
    Notification::fake();

    $sales = User::factory()->create();
    $sales->assignRole('Sales');

    $owner = User::factory()->create();
    $owner->assignRole('Sales');

    $stage = LeadStage::where(
        'slug',
        'new'
    )->firstOrFail();

    $response = $this
        ->actingAs($sales)
        ->post('/admin/leads', [
            'title' => 'ERP Development',
            'company_name' => 'Acme Ltd',
            'stage_id' => $stage->id,
            'source_id' => null,
            'owner_id' => $owner->id,
            'estimated_value' => 500000,
            'currency' => 'LKR',
            'priority' => 'high',
            'next_follow_up_at' => now()->addDay()
                ->format(
                    'Y-m-d H:i:s'
                ),
            'notes' => 'Potential ERP project',

            'contact' => [
                'name' => 'John Client',
                'designation' => 'Director',
                'email' => 'john@example.com',
                'phone' => '0771234567',
            ],
        ]);

    $response->assertRedirect();

    $lead = Lead::where(
        'company_name',
        'Acme Ltd'
    )->firstOrFail();

    expect($lead->lead_number)
        ->toStartWith('LEAD-');

    expect(
        $lead->contacts()->count()
    )->toBe(1);

    Notification::assertSentTo(
        $owner,
        LeadAssignedNotification::class
    );
});
