<?php

use App\Models\User;
use App\Notifications\AccountStatusChangedNotification;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(RolePermissionSeeder::class);
});

it('notifies a user when account status changes', function (): void {
    Notification::fake();

    $admin = User::factory()->create();
    $admin->assignRole('Admin');

    $user = User::factory()->create([
        'status' => 'active',
    ]);

    $user->assignRole('Employee');

    $this
        ->actingAs($admin)
        ->put(
            "/admin/users/{$user->public_id}",
            [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => null,
                'status' => 'suspended',
                'roles' => ['Employee'],
            ]
        )
        ->assertRedirect(
            '/admin/users'
        );

    Notification::assertSentTo(
        $user,
        AccountStatusChangedNotification::class
    );
});
