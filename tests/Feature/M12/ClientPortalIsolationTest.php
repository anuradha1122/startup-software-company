<?php

use App\Models\ClientPortalUser;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('does not allow a portal user to view another client project', function (): void {
    $projectA =
        Project::factory()->create();

    $projectB =
        Project::factory()->create();

    $portalUser =
        ClientPortalUser::create([
            'client_id' => $projectA->client_id,

            'name' => 'Portal User',

            'email' => 'portal@example.com',

            'password' => bcrypt('password'),

            'status' => 'active',
        ]);

    $this
        ->actingAs(
            $portalUser,
            'client_portal'
        )
        ->get(
            "/portal/projects/{$projectB->public_id}"
        )
        ->assertNotFound();
});
