<?php

use App\Models\User;
use App\Notifications\UserWelcomeNotification;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(RolePermissionSeeder::class);
});

it('allows an admin to create a user', function (): void {
    Notification::fake();

    $admin = User::factory()->create();
    $admin->assignRole('Admin');

    $response = $this
        ->actingAs($admin)
        ->post('/admin/users', [
            'name' => 'Test Employee',
            'email' => 'employee@example.com',
            'phone' => '0771234567',
            'status' => 'active',
            'roles' => ['Employee'],
        ]);

    $response->assertRedirect();

    $user = User::query()
        ->where(
            'email',
            'employee@example.com'
        )
        ->firstOrFail();

    expect($user->hasRole('Employee'))
        ->toBeTrue();

    Notification::assertSentTo(
        $user,
        UserWelcomeNotification::class
    );
});

it('prevents ordinary employees from managing users', function (): void {
    $employee = User::factory()->create();
    $employee->assignRole('Employee');

    $this
        ->actingAs($employee)
        ->get('/admin/users')
        ->assertForbidden();
});
