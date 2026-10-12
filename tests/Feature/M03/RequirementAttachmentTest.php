<?php

use App\Models\Client;
use App\Models\Requirement;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Storage::fake('local');

    $this->seed(
        RolePermissionSeeder::class
    );
});

it('stores requirement attachments privately', function (): void {
    $pm = User::factory()->create();
    $pm->assignRole(
        'Project Manager'
    );

    $client = Client::create([
        'client_number' => 'CLI-ATT-01',

        'name' => 'Attachment Client',

        'status' => 'active',

        'created_by' => $pm->id,
    ]);

    $requirement =
        Requirement::create([
            'requirement_number' => 'REQ-ATT-01',

            'client_id' => $client->id,

            'title' => 'Attachment Requirement',

            'status' => 'draft',

            'created_by' => $pm->id,
        ]);

    $version =
        $requirement
            ->versions()
            ->create([
                'version_number' => 1,

                'status' => 'draft',

                'created_by' => $pm->id,
            ]);

    $this
        ->actingAs($pm)
        ->post(
            "/admin/requirements/{$requirement->public_id}/versions/{$version->public_id}/attachments",
            [
                'title' => 'Reference File',

                'visibility' => 'internal',

                'file' => UploadedFile::fake()
                    ->create(
                        'reference.pdf',
                        100,
                        'application/pdf'
                    ),
            ]
        )
        ->assertRedirect();

    $attachment =
        $version
            ->attachments()
            ->firstOrFail();

    expect(
        $attachment->disk
    )->toBe('local');

    Storage::disk('local')
        ->assertExists(
            $attachment->path
        );
});
