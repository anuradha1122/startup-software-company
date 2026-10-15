<?php

use App\Models\ClientPortalDocument;
use App\Models\ClientPortalUser;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Storage::fake('local');
});

it('allows same client to download explicitly published portal document', function (): void {
    $project =
        Project::factory()->create();

    $portalUser =
        ClientPortalUser::create([
            'client_id' => $project->client_id,

            'name' => 'Client User',

            'email' => 'client@example.com',

            'password' => bcrypt('password'),

            'status' => 'active',
        ]);

    Storage::disk('local')->put(
        'portal/test.pdf',
        'safe document'
    );

    $document =
        ClientPortalDocument::create([
            'client_id' => $project->client_id,

            'project_id' => $project->id,

            'category' => 'project',

            'title' => 'Project Document',

            'original_name' => 'project.pdf',

            'disk' => 'local',

            'path' => 'portal/test.pdf',

            'mime_type' => 'application/pdf',

            'size' => 13,

            'published_at' => now(),
        ]);

    $this
        ->actingAs(
            $portalUser,
            'client_portal'
        )
        ->get(
            "/portal/documents/{$document->public_id}/download"
        )
        ->assertOk();
});
