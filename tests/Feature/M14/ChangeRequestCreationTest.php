<?php

use App\Models\ChangeRequest;
use App\Models\ClientPortalUser;
use App\Models\Project;
use Database\Seeders\CompanySettingSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed([
        CompanySettingSeeder::class,
        RolePermissionSeeder::class,
    ]);
});

it('allows portal client to submit change request for own project', function (): void {
    $project =
        Project::factory()->create([
            'status' => 'active',
        ]);

    $portalUser =
        ClientPortalUser::create([
            'client_id' => $project->client_id,

            'name' => 'Client Change User',

            'email' => 'change-client@example.com',

            'password' => bcrypt('password'),

            'status' => 'active',
        ]);

    $this
        ->actingAs(
            $portalUser,
            'client_portal'
        )
        ->post(
            "/portal/projects/{$project->public_id}/change-requests",
            [
                'title' => 'Add new report',

                'description' => 'Please add another report.',

                'business_reason' => 'Needed for operations.',

                'priority' => 'medium',
            ]
        )
        ->assertRedirect();

    $changeRequest =
        ChangeRequest::firstOrFail();

    expect(
        $changeRequest
            ->change_request_number
    )->toStartWith('CR-');

    expect(
        $changeRequest->status
    )->toBe('submitted');

    expect(
        $changeRequest
            ->client_portal_user_id
    )->toBe(
        $portalUser->id
    );
});
