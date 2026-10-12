<?php

use App\Models\Client;
use App\Models\Requirement;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(
        RolePermissionSeeder::class
    );
});

it('creates a new version without modifying previous version', function (): void {
    $pm = User::factory()->create();
    $pm->assignRole(
        'Project Manager'
    );

    $client = Client::create([
        'client_number' => 'CLI-M03-03',

        'name' => 'Revision Client',

        'status' => 'active',

        'created_by' => $pm->id,
    ]);

    $requirement =
        Requirement::create([
            'requirement_number' => 'REQ-M03-02',

            'client_id' => $client->id,

            'title' => 'Revision Requirement',

            'status' => 'revision_required',

            'owner_id' => $pm->id,

            'created_by' => $pm->id,
        ]);

    $old =
        $requirement
            ->versions()
            ->create([
                'version_number' => 1,

                'status' => 'revision_required',

                'objective' => 'Original objective',

                'functional_requirements' => [
                    'Original requirement',
                ],

                'created_by' => $pm->id,
            ]);

    $this
        ->actingAs($pm)
        ->post(
            "/admin/requirements/{$requirement->public_id}/revisions"
        )
        ->assertRedirect();

    expect(
        $requirement
            ->versions()
            ->count()
    )->toBe(2);

    $new =
        $requirement
            ->versions()
            ->where(
                'version_number',
                2
            )
            ->firstOrFail();

    expect($new->status)
        ->toBe('draft');

    expect($new->objective)
        ->toBe(
            'Original objective'
        );

    expect(
        $old->fresh()->status
    )->toBe(
        'revision_required'
    );
});
