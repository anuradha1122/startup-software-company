<?php

use App\Models\Lead;
use App\Models\LeadStage;
use App\Models\User;
use App\Notifications\LeadFollowUpDueNotification;
use App\Notifications\LeadStaleNotification;
use Database\Seeders\LeadStageSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed([
        RolePermissionSeeder::class,
        LeadStageSeeder::class,
    ]);
});

it('sends due follow up reminders', function (): void {
    Notification::fake();

    $owner = User::factory()->create();
    $owner->assignRole('Sales');

    $stage = LeadStage::where(
        'slug',
        'new'
    )->firstOrFail();

    Lead::create([
        'lead_number' => 'LEAD-REM01',
        'title' => 'Reminder Lead',
        'stage_id' => $stage->id,
        'owner_id' => $owner->id,
        'probability' => 10,
        'priority' => 'medium',
        'currency' => 'LKR',
        'status' => 'open',
        'next_follow_up_at' => now()->subMinute(),
    ]);

    $this->artisan(
        'leads:send-reminders'
    )->assertSuccessful();

    Notification::assertSentTo(
        $owner,
        LeadFollowUpDueNotification::class
    );
});

it('sends stale lead reminders', function (): void {
    Notification::fake();

    $owner = User::factory()->create();
    $owner->assignRole('Sales');

    $stage = LeadStage::where(
        'slug',
        'new'
    )->firstOrFail();

    Lead::create([
        'lead_number' => 'LEAD-REM02',
        'title' => 'Stale Lead',
        'stage_id' => $stage->id,
        'owner_id' => $owner->id,
        'probability' => 10,
        'priority' => 'medium',
        'currency' => 'LKR',
        'status' => 'open',
        'last_activity_at' => now()->subDays(8),
    ]);

    $this->artisan(
        'leads:send-reminders'
    )->assertSuccessful();

    Notification::assertSentTo(
        $owner,
        LeadStaleNotification::class
    );
});
