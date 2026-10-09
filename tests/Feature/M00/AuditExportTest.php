<?php

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(RolePermissionSeeder::class);
});

it('allows admin to export users to excel', function (): void {
    $admin = User::factory()->create();
    $admin->assignRole('Admin');

    User::factory()->count(3)->create();

    $this
        ->actingAs($admin)
        ->get('/admin/users/export')
        ->assertOk();
});

it('allows admin to export audit log as pdf', function (): void {
    $admin = User::factory()->create();
    $admin->assignRole('Admin');

    activity('test')
        ->causedBy($admin)
        ->event('test')
        ->log('Testing audit report');

    $response = $this
        ->actingAs($admin)
        ->get('/admin/audit/pdf');

    $response
        ->assertOk()
        ->assertHeader(
            'content-type',
            'application/pdf'
        );
});
