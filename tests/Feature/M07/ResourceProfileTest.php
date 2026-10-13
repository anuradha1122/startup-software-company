<?php

use App\Models\ResourceProfile;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(
        RolePermissionSeeder::class
    );
});

it('creates and updates operational resource profile', function (): void {
    $admin =
        User::factory()->create();

    $admin->assignRole('Admin');

    $person =
        User::factory()->create();

    $this
        ->actingAs($admin)
        ->put(
            "/admin/people/{$person->public_id}",
            [
                'job_title' => 'Software Engineer',

                'department' => 'Engineering',

                'capacity_hours_per_week' => 40,

                'default_allocation_percent' => 100,

                'seniority' => 'mid',

                'bio' => 'Backend developer',

                'available_for_projects' => true,
            ]
        )
        ->assertRedirect();

    $profile =
        ResourceProfile::where(
            'user_id',
            $person->id
        )->firstOrFail();

    expect(
        $profile->job_title
    )->toBe(
        'Software Engineer'
    );

    expect(
        $profile
            ->available_for_projects
    )->toBeTrue();
});
