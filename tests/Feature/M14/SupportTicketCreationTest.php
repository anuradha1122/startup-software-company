<?php

use App\Models\ClientPortalUser;
use App\Models\Project;
use App\Models\SupportTicket;
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

it('allows portal client to create support ticket for own project', function (): void {
    $project =
        Project::factory()->create();

    $portalUser =
        ClientPortalUser::create([
            'client_id' => $project->client_id,

            'name' => 'Client Support User',

            'email' => 'support-client@example.com',

            'password' => bcrypt('password'),

            'status' => 'active',
        ]);

    $this
        ->actingAs(
            $portalUser,
            'client_portal'
        )
        ->post(
            '/portal/support',
            [
                'project_id' => $project->id,

                'subject' => 'Production issue',

                'description' => 'We need assistance.',

                'priority' => 'high',

                'severity' => 'major',
            ]
        )
        ->assertRedirect();

    $ticket =
        SupportTicket::firstOrFail();

    expect(
        $ticket->ticket_number
    )->toStartWith('SUP-');

    expect(
        $ticket->client_id
    )->toBe(
        $project->client_id
    );

    expect(
        $ticket->client_portal_user_id
    )->toBe(
        $portalUser->id
    );

    expect(
        $ticket->first_response_due_at
    )->not->toBeNull();

    expect(
        $ticket->resolution_due_at
    )->not->toBeNull();
});
