<?php

use App\Models\Invoice;
use App\Models\Project;
use App\Models\User;
use App\Notifications\OperationalHealthAlertNotification;
use App\Services\Operations\OperationalHealthAlertService;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Notification::fake();
    Cache::flush();

    $this->seed(
        RolePermissionSeeder::class
    );
});

it('sends one operational warning per day when issues exist', function (): void {
    $admin =
        User::factory()->create();

    $admin->assignRole('Admin');

    $project =
        Project::factory()->create();

    Invoice::create([
        'invoice_number' => 'INV-M13-001',

        'client_id' => $project->client_id,

        'project_id' => $project->id,

        'status' => 'overdue',

        'invoice_date' => today()->subMonth(),

        'due_date' => today()->subDay(),

        'currency' => 'LKR',

        'billing_name' => $project->client->name,

        'total_amount' => 10000,

        'balance_due' => 10000,
    ]);

    $service =
        app(
            OperationalHealthAlertService::class
        );

    $service->run();
    $service->run();

    Notification::assertSentToTimes(
        $admin,
        OperationalHealthAlertNotification::class,
        1
    );
});
