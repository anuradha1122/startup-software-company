<?php

use App\Models\Client;
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

it('approves a requirement version', function (): void {
    $pm = User::factory()->create();
    $pm->assignRole(
        'Project Manager'
    );

    $client = Client::create([
        'client_number' => 'CLI-M03-02',

        'name' => 'Approval Client',

        'status' => 'active',

        'created_by' => $pm->id,
    ]);

    $contact =
        $client
            ->contacts()
            ->create([
                'name' => 'Client Approver',

                'email' => 'approver@example.com',

                'is_primary' => true,

                'status' => 'active',
            ]);

    $requirement =
        Requirement::create([
            'requirement_number' => 'REQ-M03-01',

            'client_id' => $client->id,

            'title' => 'Approval Requirement',

            'status' => 'in_review',

            'owner_id' => $pm->id,

            'created_by' => $pm->id,
        ]);

    $version =
        $requirement
            ->versions()
            ->create([
                'version_number' => 1,

                'status' => 'in_review',

                'created_by' => $pm->id,
            ]);

    $this
        ->actingAs($pm)
        ->post(
            "/admin/requirements/{$requirement->public_id}/versions/{$version->public_id}/approval-request",
            [
                'client_contact_id' => $contact->id,
            ]
        )
        ->assertRedirect();

    $approval =
        $version
            ->approvals()
            ->firstOrFail();

    $this
        ->actingAs($pm)
        ->post(
            "/admin/requirements/{$requirement->public_id}/versions/{$version->public_id}/approvals/{$approval->public_id}/decision",
            [
                'decision' => 'approved',

                'decision_notes' => 'Approved by client',
            ]
        )
        ->assertRedirect();

    $requirement->refresh();
    $version->refresh();

    expect(
        $version->status
    )->toBe('approved');

    expect(
        $requirement->status
    )->toBe('approved');

    expect(
        $requirement
            ->approved_version_id
    )->toBe($version->id);
});
